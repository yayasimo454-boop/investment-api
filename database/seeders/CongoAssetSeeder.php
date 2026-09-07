<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\AssetVolatilityProfile;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CongoAssetSeeder extends Seeder
{
    public function run(): void
    {
        $xaf = Currency::where('code', 'XAF')->first();
        $usd = Currency::where('code', 'USD')->first();

        $assets = [
            ['symbol' => 'RAWBK', 'name' => 'Rawbank', 'type' => 'action', 'sector' => 'banque', 'currency_id' => $xaf->id, 'price' => 9800, 'mu' => 0.025, 'sigma' => 0.02],
            ['symbol' => 'TMB', 'name' => 'Trust Merchant Bank', 'type' => 'action', 'sector' => 'banque', 'currency_id' => $xaf->id, 'price' => 7600, 'mu' => 0.02, 'sigma' => 0.022],
            ['symbol' => 'VODAC', 'name' => 'Vodacom Congo', 'type' => 'action', 'sector' => 'télécom', 'currency_id' => $xaf->id, 'price' => 11200, 'mu' => 0.02, 'sigma' => 0.018],
            ['symbol' => 'AIRTEL-RDC', 'name' => 'Airtel Congo', 'type' => 'action', 'sector' => 'télécom', 'currency_id' => $xaf->id, 'price' => 9400, 'mu' => 0.018, 'sigma' => 0.02],
            ['symbol' => 'ORANGE-RDC', 'name' => 'Orange RD Congo', 'type' => 'action', 'sector' => 'télécom', 'currency_id' => $xaf->id, 'price' => 8700, 'mu' => 0.017, 'sigma' => 0.019],
            ['symbol' => 'BRALIMA', 'name' => 'Bralima', 'type' => 'action', 'sector' => 'agroalimentaire', 'currency_id' => $xaf->id, 'price' => 6200, 'mu' => 0.015, 'sigma' => 0.015],
            ['symbol' => 'GCM', 'name' => 'Gécamines', 'type' => 'action', 'sector' => 'mines', 'currency_id' => $usd->id, 'price' => 4200, 'mu' => 0.03, 'sigma' => 0.035],
            ['symbol' => 'KIBALI', 'name' => 'Kibali Gold (Kinross/Barrick)', 'type' => 'action', 'sector' => 'mines', 'currency_id' => $usd->id, 'price' => 15800, 'mu' => 0.028, 'sigma' => 0.03],
            ['symbol' => 'KCC', 'name' => 'Kamoto Copper Company (Glencore)', 'type' => 'action', 'sector' => 'mines', 'currency_id' => $usd->id, 'price' => 9600, 'mu' => 0.027, 'sigma' => 0.032],
            ['symbol' => 'TFM', 'name' => 'Tenke Fungurume Mining (CMOC)', 'type' => 'action', 'sector' => 'mines', 'currency_id' => $usd->id, 'price' => 12400, 'mu' => 0.026, 'sigma' => 0.03],
            ['symbol' => 'SNEL', 'name' => 'Société Nationale d\'Électricité', 'type' => 'action', 'sector' => 'énergie', 'currency_id' => $xaf->id, 'price' => 5300, 'mu' => 0.01, 'sigma' => 0.015],
            ['symbol' => 'CONGO-AIR', 'name' => 'Congo Airways', 'type' => 'action', 'sector' => 'transport', 'currency_id' => $xaf->id, 'price' => 3900, 'mu' => 0.012, 'sigma' => 0.025],
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