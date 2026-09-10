<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    private const MIN_BALANCE_FOR_WITHDRAWAL = 5000;

    public function storeDeposit(Request $request)
    {
        $validated = $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $deposit = DepositRequest::create([
            'user_id' => $request->user()->id,
            'currency_id' => $validated['currency_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return response()->json($deposit->load('currency'), 201);
    }

    public function storeWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $user = $request->user();

        $totalBalance = Wallet::where('user_id', $user->id)->sum('balance');

        if ($totalBalance < self::MIN_BALANCE_FOR_WITHDRAWAL) {
            return response()->json([
                'message' => 'Le retrait est autorisé uniquement si l\'utilisateur a déjà effectué au moins un dépôt de 50 $.',
            ], 422);
        }

        $wallet = Wallet::where('user_id', $user->id)
            ->where('currency_id', $validated['currency_id'])
            ->first();

        if (! $wallet) {
            return response()->json(['message' => 'Aucun portefeuille trouvé pour cette devise.'], 422);
        }

        if ($wallet->balance < $validated['amount']) {
            return response()->json(['message' => 'Solde insuffisant pour ce retrait.'], 422);
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'currency_id' => $validated['currency_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return response()->json($withdrawal->load('currency'), 201);
    }

    public function myRequests(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'deposits' => DepositRequest::with('currency')->where('user_id', $userId)->orderBy('created_at', 'desc')->get(),
            'withdrawals' => WithdrawalRequest::with('currency')->where('user_id', $userId)->orderBy('created_at', 'desc')->get(),
        ]);
    }
}