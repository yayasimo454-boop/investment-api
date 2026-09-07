<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class FictionalUserController extends Controller
{
    /**
     * Retourne un échantillon aléatoire d'utilisateurs fictifs
     * (utilisé pour animer le flux d'activité en direct).
     */
    public function sample()
    {
        $users = User::where('is_fictional', true)
            ->inRandomOrder()
            ->limit(60)
            ->get(['id', 'name', 'badge']);

        return response()->json($users);
    }
}