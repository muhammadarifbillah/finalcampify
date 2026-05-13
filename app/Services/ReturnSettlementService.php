<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ReturnEscrow;
use Carbon\Carbon;

class ReturnSettlementService
{
    public function dailyFine(): int
    {
        return (int) config('returns.daily_fine', 10000);
    }

    public function calculateExpectedDateForOrder($order): ?Carbon
    {
        $latest = null;

        foreach ($order->details as $detail) {
            if (($detail->type ?? null) !== 'rent') {
                continue;
            }

            if (empty($detail->start_date) || empty($detail->duration)) {
                continue;
            }

            $start = Carbon::parse($detail->start_date)->startOfDay();
            $end = (clone $start)->addDays((int) $detail->duration)->startOfDay();

            if ($latest === null || $end->greaterThan($latest)) {
                $latest = $end;
            }
        }

        return $latest;
    }

    public function calculateLateDays(?Carbon $expectedDate, ?Carbon $actualDate): int
    {
        if ($expectedDate === null || $actualDate === null) {
            return 0;
        }

        $expected = $expectedDate->copy()->startOfDay();
        $actual = $actualDate->copy()->startOfDay();
        $diff = $expected->diffInDays($actual, false);

        return max(0, (int) $diff);
    }

    public function formatLateDuration(?Carbon $expectedDate, ?Carbon $actualDate): string
    {
        if (!$expectedDate) return '-';
        
        $actual = $actualDate ?: now();
        
        if ($actual->lessThanOrEqualTo($expectedDate)) {
            return 'Tepat Waktu';
        }

        // Hitung selisih dalam satuan terkecil yang relevan
        $diffTotal = $expectedDate->diff($actual);

        $parts = [];
        if ($diffTotal->m > 0) {
            $parts[] = $diffTotal->m . ' Bulan';
        }
        if ($diffTotal->d > 0) {
            $parts[] = $diffTotal->d . ' Hari';
        }
        if ($diffTotal->h > 0) {
            $parts[] = $diffTotal->h . ' Jam';
        }
        if ($diffTotal->i > 0) {
            $parts[] = $diffTotal->i . ' Menit';
        }

        return count($parts) > 0 ? implode(' ', $parts) : 'Kurang dari 1 Menit';
    }


    public function calculateLateFee($return): int
    {
        if ($return->type !== ReturnEscrow::TYPE_SEWA) {
            return 0;
        }

        $expected = $return->expected_date ? Carbon::parse($return->expected_date) : null;
        $actual = $return->actual_date ? Carbon::parse($return->actual_date) : null;

        $daysLate = $this->calculateLateDays($expected, $actual);
        
        $order = $return->order ?? null;
        if (!$order) return 0;

        $dailyRentTotal = $this->rentalSubtotal($order);
        
        // Denda = 30% dari total biaya sewa harian per hari keterlambatan
        $finePerDay = (int) ($dailyRentTotal * 0.3);

        return $daysLate * $finePerDay;
    }

    public function rentalSubtotal($order): int
    {
        $subtotal = 0;

        foreach ($order->details as $detail) {
            if (($detail->type ?? null) !== 'rent') {
                continue;
            }

            $qty = (int) ($detail->qty ?? 1);
            $price = (int) ($detail->harga ?? 0);
            $subtotal += ($price * max(1, $qty));
        }

        return $subtotal;
    }

    public function applyAutoCalculations($return)
    {
        $lateFee = $this->calculateLateFee($return);
        $damageFee = $this->moneyStringToInt($return->damage_fee);
        
        $return->late_fee = (string) $lateFee;
        $return->denda = $lateFee;

        if ($return->type === ReturnEscrow::TYPE_SEWA) {
            // Because order is a relation, it might return a specific Model class depending on caller context
            $order = $return->order ?? null;
            
            // Getting the first detail to fetch the product (which has buy_price)
            $firstDetail = $order ? (is_iterable($order->details) ? collect($order->details)->first() : null) : null;
            $product = $firstDetail ? $firstDetail->product : null;
            
            // Logika baru: Sewa + Jaminan (25% harga barang)
            if ($return->rental_fee_amount <= 0 && $order) {
                $return->rental_fee_amount = (string) $this->rentalSubtotal($order);
            }
            
            if ($return->deposit_amount <= 0 && $product) {
                // Deposit = 25% dari buy_price (harga barang)
                $return->deposit_amount = (string) ($product->buy_price * 0.25);
            }
            
            // Sync escrow_total
            $return->escrow_total = (string) ($return->rental_fee_amount + $return->deposit_amount);

            // Perhitungan Pembagian (untuk preview)
            $totalFines = $lateFee + $damageFee;
            $return->total_fines = (string) $totalFines;
            
            // Fee Admin Platform (10% dari Rental Fee)
            $adminFee = $return->rental_fee_amount * 0.1;
            
            // Penjual dapat: (Sewa - Fee Admin) + (Denda yang tercover deposit)
            $fineCoveredByDeposit = min($totalFines, $return->deposit_amount);
            $return->to_seller = (string) (($return->rental_fee_amount - $adminFee) + $fineCoveredByDeposit);
            
            // Pembeli dapat: Sisa deposit
            $return->to_buyer = (string) max(0, $return->deposit_amount - $totalFines);
            
            // Defisit: Denda yang tidak tercover deposit
            $return->deficit = (string) max(0, $totalFines - $return->deposit_amount);

            if (empty($return->expected_date) && $order) {
                $expected = $this->calculateExpectedDateForOrder($order);
                if ($expected) $return->expected_date = $expected;
            }
        } elseif ($return->type === ReturnEscrow::TYPE_JUAL_BELI) {
            // Pada Jual Beli, Escrow Total biasanya 100% harga produk
            // Denda/Potongan (damage_fee) mengurangi refund pembeli
            $return->total_fines = (string) $damageFee;
            $return->to_seller = (string) $damageFee;
            $return->to_buyer = (string) max(0, $return->escrow_total - $damageFee);
            $return->deficit = '0'; // Jual beli biasanya tidak ada defisit karena potong dari escrow 100%
        }

            if (empty($return->actual_date) && in_array($return->status, [ReturnEscrow::STATUS_CHECKING, ReturnEscrow::STATUS_COMPLETED])) {
                $return->actual_date = now();
            }

            return $return;
    }

    public function finalize($return, string $finalStatus)
    {
        $finalStatus = strtolower($finalStatus);
        if (!in_array($finalStatus, [ReturnEscrow::STATUS_COMPLETED, ReturnEscrow::STATUS_REJECTED], true)) {
            throw new \InvalidArgumentException('Invalid final status.');
        }

        $return->status = $finalStatus;
        if (empty($return->actual_date) && $finalStatus === ReturnEscrow::STATUS_COMPLETED) {
            $return->actual_date = now();
        }
        $this->applyAutoCalculations($return);

        if ($finalStatus === ReturnEscrow::STATUS_REJECTED) {
            // Jika ditolak (misal barang dibawa lari), semua escrow (sewa + deposit) kasih ke penjual
            $return->to_seller = $return->escrow_total;
            $return->to_buyer = '0';
        }

        return $return;
    }

    private function moneyStringToInt(mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_int($value)) {
            return $value;
        }

        $string = trim((string) $value);
        if ($string === '') {
            return 0;
        }

        // We store Rupiah values as whole numbers (scale 0), but columns are DECIMAL(?,2).
        if (str_contains($string, '.')) {
            $string = strstr($string, '.', true) ?: '0';
        }

        // Remove commas (just in case).
        $string = str_replace(',', '', $string);

        return (int) $string;
    }
}

