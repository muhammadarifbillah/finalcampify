<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['store'])->findOrFail($id);
        $transactions = Transaction::where('user_id', $id)->latest()->limit(10)->get();

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