<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room_Type;
use Illuminate\Http\Request;

class Room_TypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Room_Type')->only(['store']);
        $this->middleware('can:update,App\Models\Room_Type')->only(['update']);
        $this->middleware('can:delete,App\Models\Room_Type')->only(['destroy']);
        $this->middleware('can:views,App\Models\Room_Type')->only(['index']);
        $this->middleware('can:view,App\Models\Room_Type')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Room_Types = Room_Type::where('status', 1)->get();
        return response()->json($Room_Types);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Room_Type = Room_Type::where('id', $id)->where('status', 1)->first();
    
        if (!$Room_Type) {
            return response()->json(['message' => 'Room_Type not found or has been deleted'], 404);
        }
    
        return response()->json($Room_Type);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Room_Type = Room_Type::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Room_Type created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Room_Type = Room_Type::find($id);

        if (!$Room_Type) {
            return response()->json(['message' => 'Room_Type not found'], 404);
        }

        $Room_Type->name = $request->name;
        $Room_Type->save();
        return response()->json([
            "status" => true,
            "message" => "Room_Type updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $Room_Type = Room_Type::where('status', 1)->find($id);

        if (!$Room_Type) {
            return response()->json(['message' => 'Room_Type not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $Room_Type->status = 0;
        $Room_Type->save();
        
        return response()->json(['message' => 'Room_Type deleted']);
    }
}
