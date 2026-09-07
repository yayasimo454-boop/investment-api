<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function myWallets(Request $request)
    {
        $user = $request->user();

        return $user->wallets()->with('currency')->get();
    }
}