<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Speciality;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Course')->only(['store']);
        $this->middleware('can:update,App\Models\Course')->only(['update']);
        $this->middleware('can:delete,App\Models\Course')->only(['destroy']);
        $this->middleware('can:views,App\Models\Course')->only(['index']);
        $this->middleware('can:view,App\Models\Course')->only(['show']);
    }


    public function index()
    {
        $specialities = Course::where('status', 1)
           // specialities ilişkisini yükle
            ->get();

        return response()->json($specialities);
    }

   
    public function show($id)
    {
        $Course = Course::where('status', 1)->find($id); // specialities ilişkisiyle yükle

        if (!$Course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json($Course);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Course = Course::create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Course created successfully"
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Course = Course::where('status', 1)->find($id);
        if (!$Course) {
            return response()->json(['message' => 'Course not found'], 404);
        }  else {


            $Course->name = $request->name;
            $Course->save();

            return response()->json([
                "status" => true,
                "message" => "Course updated successfully"
            ]);
        }
    }

  
    public function destroy($id)
    {
        $Course = Course::where('status', 1)->find($id);

        if (!$Course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $Course->status = 0;
        $Course->save();

        return response()->json(['message' => 'Course deleted']);
    }
}
