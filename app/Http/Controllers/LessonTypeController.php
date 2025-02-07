<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson_Type;
use Illuminate\Http\Request;

class LessonTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Lesson_Type')->only(['store']);
        $this->middleware('can:update,App\Models\Lesson_Type')->only(['update']);
        $this->middleware('can:delete,App\Models\Lesson_Type')->only(['destroy']);
        $this->middleware('can:views,App\Models\Lesson_Type')->only(['index']);
        $this->middleware('can:view,App\Models\Lesson_Type')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Lesson_Types = Lesson_Type::where('status', 1)->get();
        return response()->json($Lesson_Types);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Lesson_Type = Lesson_Type::where('id', $id)->where('status', 1)->first();
    
        if (!$Lesson_Type) {
            return response()->json(['message' => 'Lesson_Type not found or has been deleted'], 404);
        }
    
        return response()->json($Lesson_Type);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Lesson_Type = Lesson_Type::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Lesson_Type created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Lesson_Type = Lesson_Type::find($id);

        if (!$Lesson_Type) {
            return response()->json(['message' => 'Lesson_Type not found'], 404);
        }

        $Lesson_Type->name = $request->name;
        $Lesson_Type->save();
        return response()->json([
            "status" => true,
            "message" => "Lesson_Type updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $Lesson_Type = Lesson_Type::where('status', 1)->find($id);

        if (!$Lesson_Type) {
            return response()->json(['message' => 'Lesson_Type not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $Lesson_Type->status = 0;
        $Lesson_Type->save();
        
        return response()->json(['message' => 'Lesson_Type deleted']);
    }
}
