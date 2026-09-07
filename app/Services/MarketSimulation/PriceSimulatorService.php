<?php

namespace App\Services\MarketSimulation;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\AssetVolatilityProfile;

class PriceSimulatorService
{
    /**
     * Fait avancer d'un "tick" tous les actifs actifs et retourne les nouveaux prix.
     */
    public function tickAll(): array
    {
        $assets = Asset::with(['latestPrice', 'volatilityProfile'])
            ->where('is_active', true)
            ->get();

        // Facteur de marché commun à ce tick (influence tous les actifs)
        $marketShock = $this->randomGaussian();

        // Facteurs sectoriels (un choc par secteur, partagé par les actifs du même secteur)
        $sectorShocks = [];

        $results = [];

        foreach ($assets as $asset) {
            $profile = $asset->volatilityProfile ?? $this->defaultProfile();
            $lastPrice = $asset->latestPrice?->price ?? 1;

            $sector = $profile->sector ?? 'default';
            if (!isset($sectorShocks[$sector])) {
                $sectorShocks[$sector] = $this->randomGaussian();
            }

            $newPrice = $this->nextPrice(
                (float) $lastPrice,
                (float) $profile->mu,
                (float) $profile->sigma,
                (float) $profile->market_factor_weight,
                (float) $profile->sector_factor_weight,
                $marketShock,
                $sectorShocks[$sector]
            );

            AssetPrice::create([
                'asset_id' => $asset->id,
                'price' => $newPrice,
                'recorded_at' => now(),
            ]);

            $results[] = [
                'asset_id' => $asset->id,
                'symbol' => $asset->symbol,
                'price' => round($newPrice, 8),
                'previous_price' => round((float) $lastPrice, 8),
                'change_percent' => $lastPrice > 0
                    ? round((($newPrice - $lastPrice) / $lastPrice) * 100, 3)
                    : 0,
            ];
        }

        return $results;
    }

    /**
     * Mouvement brownien géométrique : dt fixe (1 tick), avec facteur de marché
     * et facteur sectoriel en plus du bruit propre à l'actif.
     */
    private function nextPrice(
        float $lastPrice,
        float $mu,
        float $sigma,
        float $marketWeight,
        float $sectorWeight,
        float $marketShock,
        float $sectorShock
    ): float {
        $dt = 1 / 252 / 390; // approx. une "minute boursière" — juste une échelle de temps arbitraire

        $ownWeight = max(0, 1 - $marketWeight - $sectorWeight);
        $ownShock = $this->randomGaussian();

        $combinedShock = ($marketWeight * $marketShock)
            + ($sectorWeight * $sectorShock)
            + ($ownWeight * $ownShock);

        $drift = ($mu - 0.5 * $sigma ** 2) * $dt;
        $diffusion = $sigma * sqrt($dt) * $combinedShock;

        $newPrice = $lastPrice * exp($drift + $diffusion);

        return max($newPrice, 0.00000001); // jamais négatif ou nul
    }

    /**
     * Génère un nombre aléatoire suivant une loi normale centrée réduite
     * (méthode de Box-Muller).
     */
    private function randomGaussian(): float
    {
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();

        return sqrt(-2 * log(max($u1, 1e-10))) * cos(2 * M_PI * $u2);
    }

    private function defaultProfile(): AssetVolatilityProfile
    {
        $profile = new AssetVolatilityProfile();
        $profile->mu = 0.01;
        $profile->sigma = 0.02;
        $profile->sector = 'default';
        $profile->market_factor_weight = 0.5;
        $profile->sector_factor_weight = 0.3;

        return $profile;
    }
}