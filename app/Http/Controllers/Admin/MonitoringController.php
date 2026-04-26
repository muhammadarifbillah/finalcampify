<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('admin.monitoring', [
            'transactions' => Transaction::latest()->get()
        ]);
    }
}