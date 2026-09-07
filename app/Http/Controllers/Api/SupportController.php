<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            SupportMessage::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'asc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = SupportMessage::create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'sender' => 'user',
        ]);

        return response()->json($message, 201);
    }
}