<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Speciality;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Room')->only(['store']);
        $this->middleware('can:update,App\Models\Room')->only(['update']);
        $this->middleware('can:delete,App\Models\Room')->only(['destroy']);
        $this->middleware('can:views,App\Models\Room')->only(['index']);
        $this->middleware('can:view,App\Models\Room')->only(['show']);
    }

    public function index()
    {
        $Rooms = Room::where('status', 1)
            ->with('department')->with('room_type')->with('corp')
            ->get();

        return response()->json($Rooms);
    }

    public function show($id)
    {
        $room = Room::with(['devices.deviceType', 'department', 'room_type', 'corp'])
            ->where('status', 1)
            ->find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $devices = $room->devices->map(function ($device) {
            return [
                'id' => $device->id,
                'name' => $device->deviceType ? $device->deviceType->type_name : 'Unknown',
                'quantity' => $device->quantity,
            ];
        });

        return response()->json([
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'room_capacity' => $room->room_capacity,
                'department_name' => $room->department ? $room->department->name : 'No Department',
                'room_type_name' => $room->room_type ? $room->room_type->name : 'No Room Type',
                'status' => $room->status,
                'corp_name' => $room->corp ? $room->corp->name : 'No Corp',
            ],
            'devices' => $devices,
        ]);
    }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'room_capacity' => 'required|integer',
            'room_type_id' => 'required|exists:room_types,id',
            'corp_id' => 'required|exists:corps,id'
        ]);

        $Room = Room::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
            'room_capacity' => $request->room_capacity,
            'room_type_id' => $request->room_type_id,
            'corp_id' => $request->corp_id
        ]);

        return response()->json([
            "status" => true,
            "message" => "Room created successfully"
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'room_capacity' => 'required|integer',
            'room_type_id' => 'required|exists:room_types,id',
            'corp_id' => 'required|exists:corps,id'
        ]);

        $Room = Room::where('status', 1)->find($id);

        if (!$Room) {
            return response()->json(['message' => 'Room not found'], 404);
        } else {
            $Room->name = $request->name;
            $Room->department_id = $request->department_id;
            $Room->room_capacity = $request->room_capacity;
            $Room->room_type_id = $request->room_type_id;
            $Room->corp_id = $request->corp_id;
            $Room->save();

            return response()->json([
                "status" => true,
                "message" => "Room updated successfully"
            ]);
        }
    }


    public function destroy($id)
    {
        $Room = Room::where('status', 1)->find($id);

        if (!$Room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $Room->status = 0;
        $Room->save();

        return response()->json(['message' => 'Room deleted']);
    }
}
