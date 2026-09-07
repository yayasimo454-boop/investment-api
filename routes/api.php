<?php

use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\FictionalUserController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\RequestController as UserRequestController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupportController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);

    Route::get('/currencies', [CurrencyController::class, 'index']);
    Route::get('/assets', [AssetController::class, 'index']);
    Route::post('/market/tick', [MarketController::class, 'tick']);
    Route::get('/fictional-users/sample', [FictionalUserController::class, 'sample']);

    Route::get('/wallets', [WalletController::class, 'myWallets']);

    Route::post('/deposit-requests', [UserRequestController::class, 'storeDeposit']);
    Route::post('/withdrawal-requests', [UserRequestController::class, 'storeWithdrawal']);
    Route::get('/my-requests', [UserRequestController::class, 'myRequests']);

    Route::get('/support-messages', [SupportController::class, 'index']);
    Route::post('/support-messages', [SupportController::class, 'store']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

        Route::put('/users/{user}/wallet-balance', [AdminWalletController::class, 'updateBalance']);

        Route::get('/requests', [RequestController::class, 'index']);
        Route::post('/deposit-requests/{depositRequest}/approve', [RequestController::class, 'approveDeposit']);
        Route::post('/deposit-requests/{depositRequest}/reject', [RequestController::class, 'rejectDeposit']);
        Route::post('/withdrawal-requests/{withdrawalRequest}/approve', [RequestController::class, 'approveWithdrawal']);
        Route::post('/withdrawal-requests/{withdrawalRequest}/reject', [RequestController::class, 'rejectWithdrawal']);
        Route::post('/order-requests/{orderRequest}/approve', [RequestController::class, 'approveOrder']);
        Route::post('/order-requests/{orderRequest}/reject', [RequestController::class, 'rejectOrder']);
    });
});