<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Day;
use App\Models\Speciality;

class DayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Day')->only(['store']);
        $this->middleware('can:update,App\Models\Day')->only(['update']);
        $this->middleware('can:delete,App\Models\Day')->only(['destroy']);
        $this->middleware('can:views,App\Models\Day')->only(['index']);
        $this->middleware('can:view,App\Models\Day')->only(['show']);
    }


    public function index()
    {
        $specialities = Day::where('status', 1)
           // specialities ilişkisini yükle
            ->get();

        return response()->json($specialities);
    }

   
    public function show($id)
    {
        $Day = Day::where('status', 1)->find($id); // specialities ilişkisiyle yükle

        if (!$Day) {
            return response()->json(['message' => 'Day not found'], 404);
        }

        return response()->json($Day);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Day = Day::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Day created successfully"
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Day = Day::where('status', 1)->find($id);
        if (!$Day) {
            return response()->json(['message' => 'Day not found'], 404);
        }  else {


            $Day->name = $request->name;
            $Day->save();

            return response()->json([
                "status" => true,
                "message" => "Day updated successfully"
            ]);
        }
    }

  
    public function destroy($id)
    {
        $Day = Day::where('status', 1)->find($id);

        if (!$Day) {
            return response()->json(['message' => 'Day not found'], 404);
        }

        $Day->status = 0;
        $Day->save();

        return response()->json(['message' => 'Day deleted']);
    }
}
