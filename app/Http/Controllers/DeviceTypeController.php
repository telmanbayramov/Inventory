<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceType;

class DeviceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\DeviceType')->only(['store']);
        $this->middleware('can:update,App\Models\DeviceType')->only(['update']);
        $this->middleware('can:delete,App\Models\DeviceType')->only(['destroy']);
        $this->middleware('can:viewAny,App\Models\DeviceType')->only(['index']);
        $this->middleware('can:view,App\Models\DeviceType')->only(['show']);
    }
    public function index()
    {
        $devicetype = DeviceType::where('status', 1)->get();
        return response()->json($devicetype);
    }
    public function show($id)
    {
        $devicetype = DeviceType::find($id);
        if (!$devicetype) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
        return response()->json($devicetype);
    }
    public function store()
    {
        $data = request()->validate([
            'type_name' => 'required|string',
        ]);
        $devicetype = DeviceType::create($data);
        return response()->json($devicetype, 201);
    }

    public function update($id)
    {
        $data = request()->validate([
            'type_name' => 'required|string',
        ]);
        $devicetype = DeviceType::find($id);
        if (!$devicetype) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
        $devicetype->update($data);
        return response()->json($devicetype, 200);
    }
    
    public function destroy($id)
    {
        $devicetype = DeviceType::find($id);
        if (!$devicetype) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
        $devicetype->update(['status' => 0]);
        return response()->json(['message' => 'Equipment deactivated successfully'], 200);
    }
}
