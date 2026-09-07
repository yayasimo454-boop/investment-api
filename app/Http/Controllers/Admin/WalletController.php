<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Actualise (remplace) directement le solde d'un wallet.
     * Crée le wallet s'il n'existe pas encore pour cette devise.
     */
    public function updateBalance(Request $request, User $user)
    {
        $validated = $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'balance' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated, $user, $request) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'currency_id' => $validated['currency_id']],
                ['balance' => 0]
            );

            $oldBalance = $wallet->balance;
            $wallet->update(['balance' => $validated['balance']]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'adjustment',
                'amount' => $validated['balance'] - $oldBalance,
                'balance_after' => $validated['balance'],
                'performed_by' => $request->user()->id,
            ]);

            AuditLog::create([
                'admin_id' => $request->user()->id,
                'action' => 'wallet.balance_updated',
                'target_type' => 'Wallet',
                'target_id' => $wallet->id,
                'old_value' => ['balance' => $oldBalance],
                'new_value' => ['balance' => $validated['balance']],
            ]);

            return response()->json($wallet->fresh('currency'));
        });
    }
}