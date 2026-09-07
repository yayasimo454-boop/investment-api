<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DepositRequest;
use App\Models\OrderRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\Portfolio;
use App\Models\PortfolioHolding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        return response()->json([
            'deposits' => DepositRequest::with(['user', 'currency'])->where('status', 'pending')->get(),
            'withdrawals' => WithdrawalRequest::with(['user', 'currency'])->where('status', 'pending')->get(),
            'orders' => OrderRequest::with(['user', 'asset'])->where('status', 'pending')->get(),
        ]);
    }

    public function approveDeposit(Request $request, DepositRequest $depositRequest)
    {
        return DB::transaction(function () use ($request, $depositRequest) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $depositRequest->user_id, 'currency_id' => $depositRequest->currency_id],
                ['balance' => 0]
            );

            $newBalance = $wallet->balance + $depositRequest->amount;
            $wallet->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $depositRequest->amount,
                'balance_after' => $newBalance,
                'performed_by' => $request->user()->id,
            ]);

            $depositRequest->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
            ]);

            $this->log($request, 'deposit_request.approved', 'DepositRequest', $depositRequest->id);

            return response()->json($depositRequest->fresh());
        });
    }

    public function rejectDeposit(Request $request, DepositRequest $depositRequest)
    {
        $depositRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
        ]);

        $this->log($request, 'deposit_request.rejected', 'DepositRequest', $depositRequest->id);

        return response()->json($depositRequest->fresh());
    }

    public function approveWithdrawal(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        return DB::transaction(function () use ($request, $withdrawalRequest) {
            $wallet = Wallet::where('user_id', $withdrawalRequest->user_id)
                ->where('currency_id', $withdrawalRequest->currency_id)
                ->first();

            if (!$wallet || $wallet->balance < $withdrawalRequest->amount) {
                return response()->json(['message' => 'Solde insuffisant.'], 422);
            }

            $newBalance = $wallet->balance - $withdrawalRequest->amount;
            $wallet->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => -$withdrawalRequest->amount,
                'balance_after' => $newBalance,
                'performed_by' => $request->user()->id,
            ]);

            $withdrawalRequest->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
            ]);

            $this->log($request, 'withdrawal_request.approved', 'WithdrawalRequest', $withdrawalRequest->id);

            return response()->json($withdrawalRequest->fresh());
        });
    }

    public function rejectWithdrawal(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
        ]);

        $this->log($request, 'withdrawal_request.rejected', 'WithdrawalRequest', $withdrawalRequest->id);

        return response()->json($withdrawalRequest->fresh());
    }

    public function approveOrder(Request $request, OrderRequest $orderRequest)
    {
        return DB::transaction(function () use ($request, $orderRequest) {
            $portfolio = Portfolio::firstOrCreate(['user_id' => $orderRequest->user_id]);

            $holding = PortfolioHolding::firstOrCreate(
                ['portfolio_id' => $portfolio->id, 'asset_id' => $orderRequest->asset_id],
                ['quantity' => 0, 'average_buy_price' => 0]
            );

            if ($orderRequest->type === 'buy') {
                $totalCost = ($holding->quantity * $holding->average_buy_price)
                    + ($orderRequest->quantity * $orderRequest->requested_price);
                $newQuantity = $holding->quantity + $orderRequest->quantity;

                $holding->update([
                    'quantity' => $newQuantity,
                    'average_buy_price' => $newQuantity > 0 ? $totalCost / $newQuantity : 0,
                ]);
            } else {
                if ($holding->quantity < $orderRequest->quantity) {
                    return response()->json(['message' => 'Quantité insuffisante dans le portefeuille.'], 422);
                }

                $holding->update([
                    'quantity' => $holding->quantity - $orderRequest->quantity,
                ]);
            }

            $orderRequest->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
            ]);

            $this->log($request, 'order_request.approved', 'OrderRequest', $orderRequest->id);

            return response()->json($orderRequest->fresh());
        });
    }

    public function rejectOrder(Request $request, OrderRequest $orderRequest)
    {
        $orderRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
        ]);

        $this->log($request, 'order_request.rejected', 'OrderRequest', $orderRequest->id);

        return response()->json($orderRequest->fresh());
    }

    private function log(Request $request, string $action, string $targetType, int $targetId): void
    {
        AuditLog::create([
            'admin_id' => $request->user()->id,
            'action' => $action,
            'target_type' => 'User',
            'target_id' => $targetId,
            'old_value' => null,
            'new_value' => ['status' => 'approved'],
        ]);
    }
}