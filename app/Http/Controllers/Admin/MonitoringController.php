<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('admin.monitoring', [
            'transactions' => Transaction::latest()->get(),
            'reports' => Report::with(['reporter', 'seller', 'product'])->latest()->get(),
            'violations' => Violation::with(['seller', 'admin', 'product'])->latest()->get(),
        ]);
    }

    public function action(Request $request, User $seller)
    {
        abort_unless($seller->role === 'seller', 422);

        $data = $request->validate([
            'action' => 'required|in:warning,suspend,ban',
            'reason' => 'required|string|max:1000',
            'report_id' => 'nullable|exists:reports,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $strikeCount = Violation::where('seller_id', $seller->id)->count() + 1;
        $action = $data['action'];

        if ($strikeCount >= 3 && $action === 'warning') {
            $action = 'suspend';
        }
        if ($strikeCount >= 5) {
            $action = 'ban';
        }

        Violation::create([
            'seller_id' => $seller->id,
            'admin_id' => \Illuminate\Support\Facades\Auth::id(),
            'report_id' => $data['report_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'source' => ($data['report_id'] ?? null) ? 'report' : 'admin',
            'action' => $action,
            'strike_count' => $strikeCount,
            'reason' => $data['reason'],
        ]);

        if (!empty($data['report_id'])) {
            Report::whereKey($data['report_id'])->update(['status' => 'reviewed']);
        }

        if ($action === 'suspend') {
            $seller->update(['status' => 'suspended']);
            $seller->store?->update(['status' => 'suspended', 'catatan_admin' => $data['reason']]);
        }

        if ($action === 'ban') {
            $seller->update(['status' => 'banned']);
            $seller->store?->update(['status' => 'banned', 'catatan_admin' => $data['reason']]);
        }

        return back()->with('success', 'Aksi seller tersimpan.');
    }
}
