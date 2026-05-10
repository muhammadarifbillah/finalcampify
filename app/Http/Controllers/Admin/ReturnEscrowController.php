<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnEscrow;
use App\Services\ReturnSettlementService;
use Illuminate\Http\Request;

class ReturnEscrowController extends Controller
{
    public function jualBeli(Request $request)
    {
        $query = ReturnEscrow::query()->with(['order.user', 'order.details.product.store'])->where('type', 'jual_beli');

        if ($request->filled('status') && in_array($request->string('status')->toString(), ReturnEscrow::STATUSES, true)) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->input('from'), $request->input('to')]);
        }

        $returns = $query->latest()->paginate(10)->withQueryString();

        // Calculate stats
        $totalPermintaan = ReturnEscrow::where('type', 'jual_beli')->count();
        $butuhMediasi = ReturnEscrow::where('type', 'jual_beli')->where('status', 'dispute')->count();
        $escrowTertahan = ReturnEscrow::where('type', 'jual_beli')->whereIn('status', ['pending', 'dispute', 'checking'])->sum('escrow_total');
        
        return view('admin.returns.jual_beli', compact('returns', 'totalPermintaan', 'butuhMediasi', 'escrowTertahan'));
    }

    public function sewa(Request $request, ReturnSettlementService $settlement)
    {
        $query = ReturnEscrow::query()->with(['order.user', 'order.details.product.category'])->where('type', 'sewa');

        if ($request->filled('status') && in_array($request->string('status')->toString(), ReturnEscrow::STATUSES, true)) {
            $query->where('status', $request->string('status')->toString());
        }

        $returns = $query->latest()->paginate(10)->withQueryString();

        // Sync late fees for active returns to ensure index data is accurate
        foreach ($returns as $item) {
            if (!in_array($item->status, ['completed', 'rejected'])) {
                $settlement->applyAutoCalculations($item);
                $item->save();
            }
        }

        // Calculate stats
        $diharapkanHariIni = ReturnEscrow::where('type', 'sewa')->whereDate('expected_date', today())->count();
        $overdue = ReturnEscrow::where('type', 'sewa')->where('expected_date', '<', today())->whereNotIn('status', ['completed', 'rejected'])->count();
        $dalamPemeriksaan = ReturnEscrow::where('type', 'sewa')->where('status', 'checking')->count();

        return view('admin.returns.sewa', compact('returns', 'diharapkanHariIni', 'overdue', 'dalamPemeriksaan'));
    }

    public function exportSewa()
    {
        $returns = ReturnEscrow::with(['order.user', 'order.details.product'])
            ->where('type', 'sewa')
            ->latest()
            ->get();

        $filename = "pengembalian-sewa-" . now()->format('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['ID Return', 'Produk', 'Penyewa', 'SLA Kembali', 'Escrow Total', 'Denda Telat', 'Denda Rusak', 'Status']);

        foreach ($returns as $item) {
            fputcsv($handle, [
                '#RT-' . (99200 + $item->id),
                $item->order->details->first()->product->name ?? '-',
                $item->order->user->name ?? '-',
                $item->expected_date ? $item->expected_date->format('Y-m-d H:i') : '-',
                $item->escrow_total,
                $item->late_fee,
                $item->damage_fee,
                strtoupper($item->status)
            ]);
        }

        fclose($handle);
        exit;
    }

    public function index(Request $request)
    {
        return redirect()->route('admin.returns.jual_beli');
    }


    public function show(ReturnEscrow $returnEscrow, ReturnSettlementService $settlement)
    {
        $returnEscrow->load(['order.details.product.store', 'order.user']);

        // Suggest calculated fields for view display.
        $returnEscrow = $settlement->applyAutoCalculations($returnEscrow);

        if ($returnEscrow->type === 'sewa') {
            // Fetch live conversation if it exists
            $conversation = null;
            $order = $returnEscrow->order;
            $product = $order->details->first()->product ?? null;
            
            if ($product) {
                $conversation = \App\Models\Conversation::where('buyer_id', $order->user_id)
                    ->where('seller_id', $product->store->user_id)
                    ->where('product_id', $product->id)
                    ->with(['messages.sender'])
                    ->first();
            }

            // Show dispute view if it's currently a dispute OR if it was completed with damage/chat logs
            if ($returnEscrow->status === 'dispute' || ($returnEscrow->status === 'completed' && ($returnEscrow->damage_fee > 0 || !empty($returnEscrow->dispute_chat_log)))) {
                return view('admin.returns.show_sewa_dispute', [
                    'return' => $returnEscrow,
                    'statuses' => ReturnEscrow::STATUSES,
                    'conversation' => $conversation,
                ]);
            }
            return view('admin.returns.show_sewa_normal', [
                'return' => $returnEscrow,
                'statuses' => ReturnEscrow::STATUSES,
            ]);
        }

        if ($returnEscrow->type === 'jual_beli') {
            // Fetch live conversation if it exists
            $conversation = null;
            $order = $returnEscrow->order;
            $product = $order->details->first()->product ?? null;
            
            if ($product) {
                $conversation = \App\Models\Conversation::where('buyer_id', $order->user_id)
                    ->where('seller_id', $product->store->user_id)
                    ->where('product_id', $product->id)
                    ->with(['messages.sender'])
                    ->first();
            }

            if ($returnEscrow->status === 'dispute') {
                return view('admin.returns.show_jual_beli_dispute', [
                    'return' => $returnEscrow,
                    'statuses' => ReturnEscrow::STATUSES,
                    'conversation' => $conversation,
                ]);
            }
            return view('admin.returns.show_jual_beli_normal', [
                'return' => $returnEscrow,
                'statuses' => ReturnEscrow::STATUSES,
            ]);
        }

        return view('admin.returns.show', [
            'return' => $returnEscrow,
            'types' => ReturnEscrow::TYPES,
            'statuses' => ReturnEscrow::STATUSES,
        ]);
    }

    public function update(Request $request, ReturnEscrow $returnEscrow, ReturnSettlementService $settlement)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', ReturnEscrow::TYPES),
            'status' => 'required|in:' . implode(',', ReturnEscrow::STATUSES),
            'escrow_total' => 'required|numeric|min:0',
            'expected_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'damage_fee' => 'nullable|numeric|min:0',
        ]);

        $returnEscrow->fill([
            'type' => $validated['type'],
            'status' => $validated['status'],
            'escrow_total' => (string) $validated['escrow_total'],
            'expected_date' => $validated['expected_date'] ?? null,
            'actual_date' => $validated['actual_date'] ?? null,
            'damage_fee' => isset($validated['damage_fee']) ? (string) $validated['damage_fee'] : $returnEscrow->damage_fee,
        ]);

        $returnEscrow->loadMissing(['order.details']);
        $settlement->applyAutoCalculations($returnEscrow);
        $returnEscrow->save();

        $message = $request->input('action') === 'delay' ? 'Keputusan ditunda dan data telah disimpan.' : 'Data pengembalian berhasil diperbarui.';

        return redirect()
            ->route('admin.returns.show', $returnEscrow->id)
            ->with('success', $message);
    }

    public function finalize(Request $request, ReturnEscrow $returnEscrow, ReturnSettlementService $settlement)
    {
        $data = $request->validate([
            'final_status' => 'required|in:' . ReturnEscrow::STATUS_COMPLETED . ',' . ReturnEscrow::STATUS_REJECTED,
        ]);

        $returnEscrow->load(['order.details']);

        $settlement->finalize($returnEscrow, $data['final_status']);
        $returnEscrow->save();

        return redirect()
            ->route('admin.returns.sewa')
            ->with('success', 'Settlement berhasil disimpan dan transaksi diselesaikan.');
    }

    public function sendMediationMessage(Request $request, ReturnEscrow $returnEscrow)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        \App\Models\Message::create([
            'conversation_id' => $validated['conversation_id'],
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Pesan mediasi terkirim.');
    }
}
