<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Equipment')->only(['store']);
        $this->middleware('can:update,App\Models\Equipment')->only(['update']);
        $this->middleware('can:delete,App\Models\Equipment')->only(['destroy']);
        $this->middleware('can:viewAny,App\Models\Equipment')->only(['index']);
        $this->middleware('can:view,App\Models\Equipment')->only(['show']);
    }
    public function index()
    {
        $equipments = Equipment::where('status', 1)->get();
        return response()->json($equipments);
    }
    public function show($id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }

        return response()->json($equipment);
    }
    public function store()
    {
        $data = request()->validate([
            'name' => 'required|string',
        ]);

        $equipment = Equipment::create($data);

        return response()->json($equipment, 201);
    }
    public function update($id)
    {
        $data = request()->validate([
            'name' => 'required|string',
        ]);
        $equipment = Equipment::find($id);
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
        $equipment->update($data);
        return response()->json($equipment, 200);
    }
    public function destroy($id)
    {
        $equipment = Equipment::find($id);
    
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
    
        $equipment->update(['status' => 0]);
    
        return response()->json(['message' => 'Equipment deactivated successfully'], 200);
    }
   
}
