<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentRoom;

class EquipmentRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\EquipmentRoom')->only(['store']);
        $this->middleware('can:update,App\Models\EquipmentRoom')->only(['update']);
        $this->middleware('can:delete,App\Models\EquipmentRoom')->only(['destroy']);
        $this->middleware('can:viewAny,App\Models\EquipmentRoom')->only(['index']);
        $this->middleware('can:view,App\Models\EquipmentRoom')->only(['show']);
    }
    public function index()
    {
        $equipmentRooms = EquipmentRoom::where('status', 1)->get();
        return response()->json($equipmentRooms);
    }
    public function show($id)
    {
        $equipmentRoom = EquipmentRoom::find($id);

        if (!$equipmentRoom) {
            return response()->json(['error' => 'EquipmentRoom not found'], 404);
        }

        return response()->json($equipmentRoom);
    }
    public function store()
    {
        $data = request()->validate([
            'equipment_id' => 'required|integer',
            'room_id' => 'required|integer',
            'quantity' => 'required|integer',
            'status' => 'required|integer',
            'equipment_status' => 'required|integer',
        ]);

        $equipmentRoom = EquipmentRoom::create($data);

        return response()->json($equipmentRoom, 201);
    }
    public function update($id)
    {
        $data = request()->validate([
            'equipment_id' => 'required|integer',
            'room_id' => 'required|integer',
            'quantity' => 'required|integer',
            'status' => 'required|integer',
            'equipment_status' => 'required|integer',
        ]);
        $equipmentRoom = EquipmentRoom::find($id);
        if (!$equipmentRoom) {
            return response()->json(['error' => 'EquipmentRoom not found'], 404);
        }
        $equipmentRoom->update($data);
        return response()->json($equipmentRoom, 200);
    }
    public function destroy($id)
    {
        $equipmentRoom = EquipmentRoom::find($id);

        if (!$equipmentRoom) {
            return response()->json(['error' => 'EquipmentRoom not found'], 404);
        }

        $equipmentRoom->delete();
    }
  
}
