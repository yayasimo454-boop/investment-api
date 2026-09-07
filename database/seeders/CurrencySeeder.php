<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'XAF', 'name' => 'Franc CFA (BEAC)', 'symbol' => 'FCFA', 'exchange_rate_to_pivot' => 610.00],
            ['code' => 'XOF', 'name' => 'Franc CFA (BCEAO)', 'symbol' => 'FCFA', 'exchange_rate_to_pivot' => 610.00],
            ['code' => 'NGN', 'name' => 'Naira nigérian', 'symbol' => '₦', 'exchange_rate_to_pivot' => 1550.00],
            ['code' => 'GHS', 'name' => 'Cedi ghanéen', 'symbol' => 'GH₵', 'exchange_rate_to_pivot' => 15.50],
            ['code' => 'ZAR', 'name' => 'Rand sud-africain', 'symbol' => 'R', 'exchange_rate_to_pivot' => 18.20],
            ['code' => 'EGP', 'name' => 'Livre égyptienne', 'symbol' => 'E£', 'exchange_rate_to_pivot' => 48.50],
            ['code' => 'USD', 'name' => 'Dollar américain', 'symbol' => '$', 'exchange_rate_to_pivot' => 1.00],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate_to_pivot' => 0.92],
            ['code' => 'GBP', 'name' => 'Livre sterling', 'symbol' => '£', 'exchange_rate_to_pivot' => 0.79],
            ['code' => 'CNY', 'name' => 'Yuan chinois', 'symbol' => '¥', 'exchange_rate_to_pivot' => 7.20],
            ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿', 'exchange_rate_to_pivot' => 0.000011],
            ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ', 'exchange_rate_to_pivot' => 0.00034],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}