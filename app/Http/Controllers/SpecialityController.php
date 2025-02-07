<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Models\Speciality;

class SpecialityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Speciality')->only(['store']);
        $this->middleware('can:update,App\Models\Speciality')->only(['update']);
        $this->middleware('can:delete,App\Models\Speciality')->only(['destroy']);
        $this->middleware('can:views,App\Models\Speciality')->only(['index']);
        $this->middleware('can:view,App\Models\Speciality')->only(['show']);
    }

    /**
     * @OA\Get(
     *     path="/api/specialities",
     *     summary="Get all active specialities with faculty details",
     *     tags={"Specialities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of active specialities with faculty details",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="Speciality ID"),
     *                 @OA\Property(property="name", type="string", description="Speciality name"),
     *                 @OA\Property(property="status", type="integer", description="Speciality status"),
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
        $specialities = Speciality::where('status', 1)
            ->with('faculty') // Faculty ilişkisini yükle
            ->get();

        return response()->json($specialities);
    }

    /**
     * @OA\Get(
     *     path="/api/specialities/{id}",
     *     summary="Get a specific speciality with faculty details",
     *     tags={"Specialities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the speciality",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the speciality with faculty details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Speciality ID"),
     *             @OA\Property(property="name", type="string", description="Speciality name"),
     *             @OA\Property(property="status", type="integer", description="Speciality status"),
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
     *         description="Speciality not found"
     *     )
     * )
     */
    public function show($id)
    {
        $speciality = Speciality::with('faculty')->with('groups')->where('status', 1)->find($id); // Faculty ilişkisiyle yükle

        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }

        return response()->json($speciality);
    }

    /**
     * @OA\Post(
     *     path="/api/specialities",
     *     summary="Create a new speciality",
     *     tags={"Specialities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Name of the speciality"),
     *             @OA\Property(property="faculty_id", type="integer", description="ID of the faculty")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Speciality created successfully",
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

        $speciality = Speciality::create([
            'name' => $request->name,
            'faculty_id' => $request->faculty_id,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Speciality created successfully"
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/specialities/{id}",
     *     summary="Update an existing speciality",
     *     tags={"Specialities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the speciality to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Updated name of the speciality")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Speciality updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Speciality not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'faculty_id' => 'required|integer|exists:faculties,id',
        ]);

        $speciality = Speciality::where('status', 1)->find($id);
        $faculty = Faculty::where('status', 1)->find($request->faculty_id);
        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        } elseif (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        } else {


            $speciality->name = $request->name;
            $speciality->faculty_id = $request->faculty_id;
            $speciality->save();

            return response()->json([
                "status" => true,
                "message" => "Speciality updated successfully"
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/specialities/{id}",
     *     summary="Delete a speciality (soft delete by setting status=0)",
     *     tags={"Specialities"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Speciality to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Speciality deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Speciality not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $speciality = Speciality::where('status', 1)->find($id);

        if (!$speciality) {
            return response()->json(['message' => 'Speciality not found'], 404);
        }

        $speciality->status = 0;
        $speciality->save();

        return response()->json(['message' => 'Speciality deleted']);
    }
}
