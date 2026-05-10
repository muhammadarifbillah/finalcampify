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

    public function calculateExpectedDateForOrder(Order $order): ?Carbon
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

        // Use Carbon's diffForHumans for a natural feel, but customized
        return $expectedDate->diffForHumans($actual, [
            'syntax' => Carbon::DIFF_ABSOLUTE,
            'parts' => 2,
            'skip' => ['week'],
        ]);
    }


    public function calculateLateFee(ReturnEscrow $return): int
    {
        if ($return->type !== ReturnEscrow::TYPE_SEWA) {
            return 0;
        }

        $expected = $return->expected_date ? Carbon::parse($return->expected_date) : null;
        $actual = $return->actual_date ? Carbon::parse($return->actual_date) : null;

        $daysLate = $this->calculateLateDays($expected, $actual);

        return $daysLate * $this->dailyFine();
    }

    public function rentalSubtotal(Order $order): int
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

    public function applyAutoCalculations(ReturnEscrow $return): ReturnEscrow
    {
        $lateFee = $this->calculateLateFee($return);
        $damageFee = $this->moneyStringToInt($return->damage_fee);
        
        $return->late_fee = (string) $lateFee;
        $return->denda = $lateFee;

        if ($return->type === ReturnEscrow::TYPE_SEWA) {
            $order = $return->order;
            $product = $order?->details?->first()?->product;
            
            // Logika baru: Sewa + Jaminan (50% harga barang)
            if ($return->rental_fee_amount <= 0) {
                $return->rental_fee_amount = (string) $this->rentalSubtotal($order);
            }
            
            if ($return->deposit_amount <= 0 && $product) {
                // Deposit = 50% dari buy_price (harga barang)
                $return->deposit_amount = (string) ($product->buy_price * 0.5);
            }
            
            // Sync escrow_total
            $return->escrow_total = (string) ($return->rental_fee_amount + $return->deposit_amount);

            // Perhitungan Pembagian (untuk preview)
            $totalFines = $lateFee + $damageFee;
            $return->total_fines = (string) $totalFines;
            
            // Penjual dapat: Sewa + (Denda yang tercover deposit)
            $fineCoveredByDeposit = min($totalFines, $return->deposit_amount);
            $return->to_seller = (string) ($return->rental_fee_amount + $fineCoveredByDeposit);
            
            // Pembeli dapat: Sisa deposit
            $return->to_buyer = (string) max(0, $return->deposit_amount - $totalFines);
            
            // Defisit: Denda yang tidak tercover deposit
            $return->deficit = (string) max(0, $totalFines - $return->deposit_amount);

            if (empty($return->expected_date) && $order) {
                $expected = $this->calculateExpectedDateForOrder($order);
                if ($expected) $return->expected_date = $expected;
            }
        }

        return $return;
    }

    public function finalize(ReturnEscrow $return, string $finalStatus): ReturnEscrow
    {
        $finalStatus = strtolower($finalStatus);
        if (!in_array($finalStatus, [ReturnEscrow::STATUS_COMPLETED, ReturnEscrow::STATUS_REJECTED], true)) {
            throw new \InvalidArgumentException('Invalid final status.');
        }

        $return->status = $finalStatus;
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

