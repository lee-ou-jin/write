<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\JsonResponse;

class EquipmentController extends Controller
{
    public function index(): JsonResponse
    {
        $equipments = Equipment::query()
            ->with('ship:id,name')
            ->orderBy('id')
            ->get(['id', 'ship_id', 'name', 'type', 'serial_number']);

        $equipments->map(function (Equipment $equipment) {
            $shipName = optional($equipment->ship)->name;
            $equipment->display_name = ($shipName ? $shipName . ' - ' : '') . $equipment->name . ' (' . $equipment->type . ')';
            return $equipment;
        });

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $equipments
        ]);
    }
}
