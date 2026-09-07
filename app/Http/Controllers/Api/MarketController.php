<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MarketSimulation\PriceSimulatorService;

class MarketController extends Controller
{
    public function __construct(
        private PriceSimulatorService $simulator
    ) {}

    /**
     * Fait avancer les prix d'un tick et retourne les nouvelles valeurs.
     */
    public function tick()
    {
        $results = $this->simulator->tickAll();

        return response()->json($results);
    }
}