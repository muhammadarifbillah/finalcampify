<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('store')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = $request->q;
                $q->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->latest();

        $users = $query->paginate(10)->withQueryString();
        $roleCounts = User::selectRaw('COALESCE(role, "buyer") as role_name, COUNT(*) as total')
            ->groupByRaw('COALESCE(role, "buyer")')
            ->pluck('total', 'role_name');

        return view('admin.users', compact('users', 'roleCounts'));
    }

    public function show($id)
    {
        $user = User::with(['store'])->findOrFail($id);
        $transactionsQuery = Transaction::where('user_id', $id);
        if (Schema::hasColumn('transactions', 'created_at')) {
            $transactionsQuery->latest();
        }
        $transactions = $transactionsQuery->limit(10)->get();

        // Simulasi aktivitas login (dalam implementasi nyata, ini dari log atau event)
        $loginActivities = [
            ['action' => 'login', 'timestamp' => $user->last_login, 'ip' => '192.168.1.1'],
            // Tambahkan lebih banyak jika ada log nyata
        ];

        return view('admin.user_detail', compact('user', 'transactions', 'loginActivities'));
    }

    public function activate($id)
    {
        User::findOrFail($id)->update(['status' => 'active']);
        return back()->with('success', 'User berhasil diaktifkan.');
    }

    public function deactivate($id)
    {
        User::findOrFail($id)->update(['status' => 'inactive']);
        return back()->with('success', 'User berhasil dinonaktifkan.');
    }

    public function ban($id)
    {
        User::findOrFail($id)->update(['status' => 'banned']);
        return back()->with('success', 'User berhasil diblokir.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
