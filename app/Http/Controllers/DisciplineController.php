<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Discipline;

class DisciplineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Discipline')->only(['store']);
        $this->middleware('can:update,App\Models\Discipline')->only(['update']);
        $this->middleware('can:delete,App\Models\Discipline')->only(['destroy']);
        $this->middleware('can:views,App\Models\Discipline')->only(['index']);
        $this->middleware('can:view,App\Models\Discipline')->only(['show']);
    }


    public function index()
    {
        $specialities = Discipline::where('status', 1)
            ->with('department') // department ilişkisini yükle
            ->get();

        return response()->json($specialities);
    }

   
    public function show($id)
    {
        $Discipline = Discipline::with('department')->where('status', 1)->find($id); // department ilişkisiyle yükle

        if (!$Discipline) {
            return response()->json(['message' => 'Discipline not found'], 404);
        }

        return response()->json($Discipline);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $Discipline = Discipline::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Discipline created successfully"
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $Discipline = Discipline::where('status', 1)->find($id);
        $department = Department::where('status', 1)->find($request->department_id);
        if (!$Discipline) {
            return response()->json(['message' => 'Discipline not found'], 404);
        } elseif (!$department) {
            return response()->json(['message' => 'department not found'], 404);
        } else {


            $Discipline->name = $request->name;
            $Discipline->department_id = $request->department_id;
            $Discipline->save();

            return response()->json([
                "status" => true,
                "message" => "Discipline updated successfully"
            ]);
        }
    }

  
    public function destroy($id)
    {
        $Discipline = Discipline::where('status', 1)->find($id);

        if (!$Discipline) {
            return response()->json(['message' => 'Discipline not found'], 404);
        }

        $Discipline->status = 0;
        $Discipline->save();

        return response()->json(['message' => 'Discipline deleted']);
    }
}
