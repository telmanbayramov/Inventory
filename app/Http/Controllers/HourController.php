<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hour;
use Illuminate\Http\Request;

class HourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Hour')->only(['store']);
        $this->middleware('can:update,App\Models\Hour')->only(['update']);
        $this->middleware('can:delete,App\Models\Hour')->only(['destroy']);
        $this->middleware('can:views,App\Models\Hour')->only(['index']);
        $this->middleware('can:view,App\Models\Hour')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Hours = Hour::where('status', 1)->get();
        return response()->json($Hours);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Hour = Hour::where('id', $id)->where('status', 1)->first();
    
        if (!$Hour) {
            return response()->json(['message' => 'Hour not found or has been deleted'], 404);
        }
    
        return response()->json($Hour);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Hour = Hour::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Hour created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Hour = Hour::find($id);

        if (!$Hour) {
            return response()->json(['message' => 'Hour not found'], 404);
        }

        $Hour->name = $request->name;
        $Hour->save();
        return response()->json([
            "status" => true,
            "message" => "Hour updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $Hour = Hour::where('status', 1)->find($id);

        if (!$Hour) {
            return response()->json(['message' => 'Hour not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $Hour->status = 0;
        $Hour->save();
        
        return response()->json(['message' => 'Hour deleted']);
    }
}
