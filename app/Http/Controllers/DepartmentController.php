<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Discipline;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Department')->only(['store']);
        $this->middleware('can:update,App\Models\Department')->only(['update']);
        $this->middleware('can:delete,App\Models\Department')->only(['destroy']);
        $this->middleware('can:views,App\Models\Department')->only(['index']);
        $this->middleware('can:view,App\Models\Department')->only(['show']);
    }

    /**
     * @OA\Get(
     *     path="/api/departments",
     *     summary="Get all active departments with faculty details",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active departments with faculty details",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="Department ID"),
     *                 @OA\Property(property="name", type="string", description="Department name"),
     *                 @OA\Property(property="status", type="integer", description="Department status"),
     *                 @OA\Property(
     *                     property="faculty",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", description="Faculty ID"),
     *                     @OA\Property(property="name", type="string", description="Faculty name")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $departments = Department::where('status', 1)
            ->with('faculty')
            ->get();

        return response()->json($departments);
    }

    /**
     * @OA\Get(
     *     path="/api/departments/{id}",
     *     summary="Get a specific department with faculty details",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the department",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the department with faculty details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Department ID"),
     *             @OA\Property(property="name", type="string", description="Department name"),
     *             @OA\Property(property="status", type="integer", description="Department status"),
     *             @OA\Property(
     *                 property="faculty",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Faculty ID"),
     *                 @OA\Property(property="name", type="string", description="Faculty name")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     )
     * )
     */
    public function show($id)
    {
        $department = Department::with('faculty')->with('disciplines')->where('status', 1)->find($id); // Faculty ilişkisiyle yükle

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json($department);
    }

    /**
     * @OA\Post(
     *     path="/api/departments",
     *     summary="Create a new department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Name of the department"),
     *             @OA\Property(property="faculty_id", type="integer", description="ID of the faculty")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Department created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'faculty_id' => $request->faculty_id,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Department created successfully"
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/departments/{id}",
     *     summary="Update an existing department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the department to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Updated name of the department")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faculty_id' => 'required|integer|exists:faculties,id', // Fakülte ID'nin doğrulaması
        ]);

        $department = Department::where('status', 1)->find($id);
        $faculty = Faculty::where('status', 1)->find($request->faculty_id);
        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        } elseif (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        } else {
            // Gelen verilerle bölümü güncelle
            $department->name = $request->name;
            $department->faculty_id = $request->faculty_id;
            $department->save();

            return response()->json([
                "status" => true,
                "message" => "Department updated successfully"
            ]);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/departments/{id}",
     *     summary="Delete a department (soft delete by setting status=0)",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the department to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     )
     * )
     */
    // public function destroy($id)
    // {
    //     $department = Department::find($id);

    //     if (!$department) {
    //         return response()->json(['message' => 'Department tapılmadı'], 404);
    //     }

    //     // Fakülteye bağlı diğer kayıtları kontrol et (status = 1 olanlar)
    //     $hasActiveRelations = Discipline::where('department_id', $id)->where('status', 1)->exists() ||
    //         Room::where('department_id', $id)->where('status', 1)->exists() ||
    //         User::whereHas('departments', function ($query) use ($id) {
    //             $query->where('department_id', $id);
    //         })->where('status', 1)->exists() ||
    //         Schedule::where('department_id', $id)->where('status', 1)->exists();


    //     if ($hasActiveRelations) {
    //         return response()->json(['message' => 'Bu departmentə bağlı aktiv məlumatlar var. Silinə bilmir.'], 400);
    //     }

    //     // Fakültenin status'unu 0 yaparak soft delete işlemi uygula
    //     $department->status = 0;
    //     $department->save();

    //     return response()->json(['message' => 'Department uğurla silindi.']);
    // }
}
