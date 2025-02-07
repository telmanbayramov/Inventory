<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Week_Type;
use Illuminate\Http\Request;

class Week_TypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Week_Type')->only(['store']);
        $this->middleware('can:update,App\Models\Week_Type')->only(['update']);
        $this->middleware('can:delete,App\Models\Week_Type')->only(['destroy']);
        $this->middleware('can:views,App\Models\Week_Type')->only(['index']);
        $this->middleware('can:view,App\Models\Week_Type')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Week_Types = Week_Type::where('status', 1)->get();
        return response()->json($Week_Types);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Week_Type = Week_Type::where('id', $id)->where('status', 1)->first();
    
        if (!$Week_Type) {
            return response()->json(['message' => 'Week_Type not found or has been deleted'], 404);
        }
    
        return response()->json($Week_Type);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Week_Type = Week_Type::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Week_Type created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Week_Type = Week_Type::find($id);

        if (!$Week_Type) {
            return response()->json(['message' => 'Week_Type not found'], 404);
        }

        $Week_Type->name = $request->name;
        $Week_Type->save();
        return response()->json([
            "status" => true,
            "message" => "Week_Type updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $Week_Type = Week_Type::where('status', 1)->find($id);

        if (!$Week_Type) {
            return response()->json(['message' => 'Week_Type not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $Week_Type->status = 0;
        $Week_Type->save();
        
        return response()->json(['message' => 'Week_Type deleted']);
    }
}
