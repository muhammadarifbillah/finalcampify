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

    public function verifyKtp($id)
    {
        $user = User::findOrFail($id);
        $user->update(['ktp_verified_at' => now()]);
        return back()->with('success', 'Identitas (KTP) user berhasil diverifikasi.');
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            // Hapus data terkait yang mungkin tidak memiliki ON DELETE CASCADE di database
            $tables = [
                'product_ratings' => 'user_id',
                'store_ratings' => 'user_id',
                'wishlists' => 'user_id',
                'rentals' => 'user_id',
                'transactions' => 'user_id',
                'messages' => 'sender_id',
            ];
            
            foreach ($tables as $table => $column) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    \Illuminate\Support\Facades\DB::table($table)->where($column, $id)->delete();
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('reports')) {
                \Illuminate\Support\Facades\DB::table('reports')->where('reporter_id', $id)->orWhere('seller_id', $id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('violations')) {
                \Illuminate\Support\Facades\DB::table('violations')->where('seller_id', $id)->orWhere('admin_id', $id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('conversations')) {
                \Illuminate\Support\Facades\DB::table('conversations')->where('buyer_id', $id)->orWhere('seller_id', $id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('chats')) {
                \Illuminate\Support\Facades\DB::table('chats')
                    ->where('user_id', $id)
                    ->orWhere('sender_id', $id)
                    ->orWhere('receiver_id', $id)
                    ->delete();
            }

            User::findOrFail($id)->delete();
        });

        return back()->with('success', 'User dan seluruh data terkait berhasil dihapus.');
    }
}
