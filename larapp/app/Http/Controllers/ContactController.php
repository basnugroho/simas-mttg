<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'mosque_id' => 'nullable|exists:mosques,id',
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
        ];

        if (Schema::hasColumn('messages', 'mosque_id')) {
            $payload['mosque_id'] = $data['mosque_id'] ?? null;
        }

        $msg = Message::create($payload);

        // TODO: dispatch mail or notifications here

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
