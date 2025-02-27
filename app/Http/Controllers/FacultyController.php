<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Speciality;
use App\Models\User;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Faculty')->only(['store']);
        $this->middleware('can:update,App\Models\Faculty')->only(['update']);
        $this->middleware('can:delete,App\Models\Faculty')->only(['destroy']);
        $this->middleware('can:views,App\Models\Faculty')->only(['index']);
        $this->middleware('can:view,App\Models\Faculty')->only(['show']);
    }

    /**
     * @OA\Get(
     *     path="/api/faculties",
     *     summary="Get all active faculties",
     *     tags={"Faculties"},
     *     security={{"bearerAuth":{}}},     
     *     @OA\Response(
     *         response=200,
     *         description="List of active faculties",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="Faculty ID"),
     *                 @OA\Property(property="name", type="string", description="Faculty name"),
     *                 @OA\Property(property="status", type="integer", description="Faculty status")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $faculties = Faculty::where('status', 1)->with('departments')->get();

        return response()->json($faculties);
    }

    /**
     * @OA\Get(
     *     path="/api/faculties/{id}",
     *     summary="Get a specific faculty",
     *     tags={"Faculties"},
     *     security={{"bearerAuth":{}}},     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faculty",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the faculty",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Faculty ID"),
     *             @OA\Property(property="name", type="string", description="Faculty name"),
     *             @OA\Property(property="status", type="integer", description="Faculty status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faculty not found"
     *     )
     * )
     */
    public function show($id)
    {
        $faculty = Faculty::where('status', 1)->with('departments')->find($id);

        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        return response()->json($faculty);
    }

    /**
     * @OA\Post(
     *     path="/api/faculties",
     *     summary="Create a new faculty",
     *     tags={"Faculties"},
     *     security={{"bearerAuth":{}}},     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Name of the faculty")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Faculty created successfully",
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
        ]);

        $faculty = Faculty::create([
            'name' => $request->name,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Faculty created successfully"
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/faculties/{id}",
     *     summary="Update an existing faculty",
     *     tags={"Faculties"},
     *     security={{"bearerAuth":{}}},     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faculty to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Updated name of the faculty")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Faculty updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faculty not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $faculty = Faculty::where('status', 1)->find($id);

        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $faculty->name = $request->name;
        $faculty->save();

        return response()->json([
            "status" => true,
            "message" => "Faculty updated successfully"
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/faculties/{id}",
     *     summary="Delete a faculty (soft delete by setting status=0)",
     *     tags={"Faculties"},
     *     security={{"bearerAuth":{}}},     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faculty to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Faculty deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faculty not found"
     *     )
     * )
     */
    // public function destroy($id)
    // {
    //     $faculty = Faculty::find($id);

    //     if (!$faculty) {
    //         return response()->json(['message' => 'Fakültə tapılmadı'], 404);
    //     }

    //     // Fakülteye bağlı diğer kayıtları kontrol et (status = 1 olanlar)
    //     $hasActiveRelations = Department::where('faculty_id', $id)->where('status', 1)->exists() ||
    //         Speciality::where('faculty_id', $id)->where('status', 1)->exists() ||
    //         User::where('faculty_id', $id)->where('status', 1)->exists() ||
    //         Group::where('faculty_id', $id)->where('status', 1)->exists() ||
    //         Schedule::where('faculty_id', $id)->where('status', 1)->exists();

    //     if ($hasActiveRelations) {
    //         return response()->json(['message' => 'Bu fakültəyə bağlı aktiv məlumatlar var. Silinə bilmir.'], 400);
    //     }

    //     // Fakültenin status'unu 0 yaparak soft delete işlemi uygula
    //     $faculty->status = 0;
    //     $faculty->save();

    //     return response()->json(['message' => 'Fakültə uğurla silindi.']);
    // }
}
