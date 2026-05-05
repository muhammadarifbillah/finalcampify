<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Report;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chats', [
            'reports' => Report::with(['reporter', 'seller', 'product', 'conversation', 'message'])
                ->where('type', 'chat')
                ->latest()
                ->get(),
            'chats' => Chat::where('is_flagged', true)->latest()->get(),
        ]);
    }

    public function flag($id)
    {
        Chat::findOrFail($id)->update(['is_flagged'=>true]);
        return back();
    }
}
