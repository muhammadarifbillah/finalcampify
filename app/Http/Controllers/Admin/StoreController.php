<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::latest()->get();
        return view('admin.stores', compact('stores'));
    }

    public function ban($id)
    {
        Store::findOrFail($id)->update([
            'status'=>'banned',
            'alasan_ban'=>'Melanggar aturan'
        ]);
        return back();
    }

    public function unban($id)
    {
        Store::findOrFail($id)->update([
            'status'=>'aktif',
            'alasan_ban'=>null
        ]);
        return back();
    }
}