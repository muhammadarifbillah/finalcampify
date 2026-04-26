<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chats', [
            'chats' => Chat::latest()->get()
        ]);
    }

    public function flag($id)
    {
        Chat::findOrFail($id)->update(['is_flagged'=>true]);
        return back();
    }
}