<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDailyBonus extends Command
{
    protected $signature = 'wallets:add-daily-bonus';

    protected $description = 'Ajoute un bonus quotidien de 5$ au wallet USD de chaque utilisateur';

    private const DAILY_BONUS = 5;

    public function handle(): int
    {
        $currency = Currency::where('code', 'USD')->first();

        if (! $currency) {
            $this->error('Devise USD introuvable.');
            return self::FAILURE;
        }

        $count = 0;

        User::where('is_fictional', false)->chunkById(100, function ($users) use ($currency, &$count) {
            foreach ($users as $user) {
                DB::transaction(function () use ($user, $currency) {
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $user->id, 'currency_id' => $currency->id],
                        ['balance' => 0]
                    );

                    $newBalance = $wallet->balance + self::DAILY_BONUS;
                    $wallet->update(['balance' => $newBalance]);

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'daily_bonus',
                        'amount' => self::DAILY_BONUS,
                        'balance_after' => $newBalance,
                        'performed_by' => null,
                    ]);
                });

                $count++;
            }
        });

        $this->info("Bonus quotidien de {$this->getBonusLabel()} ajouté à {$count} utilisateur(s).");

        return self::SUCCESS;
    }

    private function getBonusLabel(): string
    {
        return self::DAILY_BONUS . '$';
    }
}