<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['currency', 'latestPrice'])
            ->where('is_active', true)
            ->get();

        return response()->json($assets);
    }
}