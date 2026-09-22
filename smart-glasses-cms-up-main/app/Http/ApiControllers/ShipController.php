<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Ship;
use App\Models\SmartGlasses;
use Illuminate\Http\JsonResponse;

class ShipController extends Controller
{
    public function index(): JsonResponse
    {
        $ships = Ship::query()->get(['id', 'name', 'ships_company_id']);
        $ships->map(function (Ship $ship) {
            $ship->company_name = $ship->shipsCompany->name;
            return $ship;
        });

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $ships
        ]);
    }

    public function positions(): JsonResponse
    {
        $positions = SmartGlasses::query()
            ->whereNotNull('position')
            ->distinct('position')
            ->get(['position']);

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $positions
        ]);
    }
}
