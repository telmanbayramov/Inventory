<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Semester')->only(['store']);
        $this->middleware('can:update,App\Models\Semester')->only(['update']);
        $this->middleware('can:delete,App\Models\Semester')->only(['destroy']);
        $this->middleware('can:views,App\Models\Semester')->only(['index']);
        $this->middleware('can:view,App\Models\Semester')->only(['show']);
    }

    
    public function index()
    {
        //die();
        $Semesters = Semester::where('status', 1)->get();
        return response()->json($Semesters);
    }

   
    public function show($id)
    {
        // Aktif kayıtları getirin
        $Semester = Semester::where('id', $id)->where('status', 1)->first();
    
        if (!$Semester) {
            return response()->json(['message' => 'Semester not found or has been deleted'], 404);
        }
    
        return response()->json($Semester);
    }

  
  
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|max:255',
            'semester_num' => 'required|string|max:255',
        ]);

        $Semester = Semester::create([
            'year' => $request->year,
            'semester_num' =>$request->semester_num
        ]);

        return response()->json([
            "status" => true,
            "message" => "Semester created successfully"
        ]);
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|string|max:255',
            'semester_num' => 'required|string|max:255',
        ]);

        $Semester = Semester::find($id);

        if (!$Semester) {
            return response()->json(['message' => 'Semester not found'], 404);
        }

        $Semester->year = $request->year;
        $Semester->semester_num = $request->semester_num;
        $Semester->save();
        return response()->json([
            "status" => true,
            "message" => "Semester updated successfully"
        ]);
    }

   
    public function destroy($id)
    {
        $Semester = Semester::where('status', 1)->find($id);

        if (!$Semester) {
            return response()->json(['message' => 'Semester not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $Semester->status = 0;
        $Semester->save();
        
        return response()->json(['message' => 'Semester deleted']);
    }
}
