<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\AssetVolatilityProfile;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $xaf = Currency::where('code', 'XAF')->first();
        $usd = Currency::where('code', 'USD')->first();

        $assets = [
            // Actions africaines
            ['symbol' => 'SONARA', 'name' => 'Société Nationale de Raffinage', 'type' => 'action', 'sector' => 'énergie', 'currency_id' => $xaf->id, 'price' => 12500, 'mu' => 0.02, 'sigma' => 0.03],
            ['symbol' => 'ENEO', 'name' => 'ENEO Cameroun', 'type' => 'action', 'sector' => 'énergie', 'currency_id' => $xaf->id, 'price' => 8900, 'mu' => 0.015, 'sigma' => 0.025],
            ['symbol' => 'SCB-CM', 'name' => 'Société Camerounaise de Banque', 'type' => 'action', 'sector' => 'banque', 'currency_id' => $xaf->id, 'price' => 15200, 'mu' => 0.03, 'sigma' => 0.02],
            ['symbol' => 'MTNC', 'name' => 'MTN Cameroun', 'type' => 'action', 'sector' => 'télécom', 'currency_id' => $xaf->id, 'price' => 6400, 'mu' => 0.025, 'sigma' => 0.02],

            // Cryptomonnaies
            ['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'sector' => 'crypto', 'currency_id' => $usd->id, 'price' => 91000, 'mu' => 0.05, 'sigma' => 0.06],
            ['symbol' => 'ETH', 'name' => 'Ethereum', 'type' => 'crypto', 'sector' => 'crypto', 'currency_id' => $usd->id, 'price' => 3200, 'mu' => 0.04, 'sigma' => 0.07],

            // Forex
            ['symbol' => 'EURXAF', 'name' => 'Euro / Franc CFA', 'type' => 'forex', 'sector' => 'forex', 'currency_id' => $xaf->id, 'price' => 655.96, 'mu' => 0.001, 'sigma' => 0.005],
            ['symbol' => 'USDXAF', 'name' => 'Dollar US / Franc CFA', 'type' => 'forex', 'sector' => 'forex', 'currency_id' => $xaf->id, 'price' => 610.00, 'mu' => 0.002, 'sigma' => 0.008],
        ];

        foreach ($assets as $data) {
            $asset = Asset::updateOrCreate(
                ['symbol' => $data['symbol']],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'sector' => $data['sector'],
                    'currency_id' => $data['currency_id'],
                    'is_active' => true,
                ]
            );

            AssetPrice::create([
                'asset_id' => $asset->id,
                'price' => $data['price'],
                'recorded_at' => now(),
            ]);

            AssetVolatilityProfile::updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'mu' => $data['mu'],
                    'sigma' => $data['sigma'],
                    'sector' => $data['sector'],
                    'market_factor_weight' => 0.5,
                    'sector_factor_weight' => 0.3,
                ]
            );
        }
    }
}