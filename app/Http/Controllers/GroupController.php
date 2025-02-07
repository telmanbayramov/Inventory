<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Speciality;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Group')->only(['store']);
        $this->middleware('can:update,App\Models\Group')->only(['update']);
        $this->middleware('can:delete,App\Models\Group')->only(['destroy']);
        $this->middleware('can:views,App\Models\Group')->only(['index']);
        $this->middleware('can:view,App\Models\Group')->only(['show']);
    }

    public function index()
    {
        $groups = Group::where('status', 1)
            ->with('faculty')->with('speciality')->with('course') 
            ->get();

        return response()->json($groups);
    }

   
    public function show($id)
    {
        $Group = Group::with('faculty')->with('speciality')->with('course')->where('status', 1)->find($id); // department ilişkisiyle yükle

        if (!$Group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json($Group);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faculty_id' => 'required|exists:faculties,id',
            'student_amount' => 'required|integer',
            'group_type' => 'required|integer',
            'course_id' => 'required|exists:faculties,id',
            'speciality_id' => 'required|exists:faculties,id',
            'group_level' => 'required|integer'
        ]);

        $Group = Group::create([
            'name' => $request->name,
            'faculty_id' => $request->faculty_id,
            'student_amount' => $request->student_amount,
            'group_type' => $request->group_type,
            'course_id' => $request->course_id,
            'speciality_id' => $request->speciality_id,
            'group_level' => $request->group_level
        ]);

        return response()->json([
            "status" => true,
            "message" => "Group created successfully"
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faculty_id' => 'required|exists:faculties,id',
            'student_amount' => 'required|integer',
            'group_type' => 'required|integer',
            'course_id' => 'required|exists:courses,id',
            'speciality_id' => 'required|exists:specialities,id',
            'group_level' => 'required|integer'
        ]);

        $Group = Group::where('status', 1)->find($id);
        $speciality = Speciality::where('faculty_id', $request->faculty_id)->where('status', 1)->find($request->speciality_id);
        if (!$Group) {
            return response()->json(['message' => 'Group not found'], 404);
        } elseif (!$speciality) {
            return response()->json(['message' => 'İxtisas bu fakültəyə aid deyil və ya ixtisas yoxdur'], 404);
        } else {
            $Group->name = $request->name;
            $Group->faculty_id = $request->faculty_id;
            $Group->student_amount = $request->student_amount;
            $Group->group_type = $request->group_type;
            $Group->course_id = $request->course_id;
            $Group->speciality_id = $request->speciality_id;
            $Group->group_level = $request->group_level;
            $Group->save();

            return response()->json([
                "status" => true,
                "message" => "Group updated successfully"
            ]);
        }
    }

  
    public function destroy($id)
    {
        $Group = Group::where('status', 1)->find($id);

        if (!$Group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $Group->status = 0;
        $Group->save();

        return response()->json(['message' => 'Group deleted']);
    }
}
