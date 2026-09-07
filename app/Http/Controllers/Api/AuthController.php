<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'is_fictional' => false,
        ]);

        $currency = Currency::where('code', 'USD')->first() ?? Currency::first();

        if ($currency) {
            $user->wallets()->create([
                'currency_id' => $currency->id,
                'balance' => 750,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('wallets.currency'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Identifiants incorrects.'],
            ]);
        }

        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'phone' => ['Ce compte a été suspendu. Contactez le support.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:30', 'unique:users,phone,' . $user->id],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return response()->json($user);
    }
}