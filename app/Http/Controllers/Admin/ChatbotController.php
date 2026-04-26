<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('admin.chatbot', [
            'data' => ChatbotResponse::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'keyword' => 'required|string|max:255',
            'response' => 'required|string',
        ]);

        ChatbotResponse::create($data);

        return redirect('/admin/chatbot')->with('success', 'Respon chatbot berhasil disimpan.');
    }
}