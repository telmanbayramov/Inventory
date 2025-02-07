<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can:create,App\Models\Permission')->only(['store']);
        $this->middleware('can:update,App\Models\Permission')->only(['update']);
        $this->middleware('can:delete,App\Models\Permission')->only(['destroy']);
        $this->middleware('can:views,App\Models\Permission')->only(['index']);
        $this->middleware('can:view,App\Models\Permission')->only(['show']);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     tags={"Permissions"},
     *     summary="List all permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of permissions"
     *     )
     * )
     */
    public function index()
    {
        $permissions = Permission::where('status', 1)->get();
        return response()->json($permissions);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions/{id}",
     *     summary="Get a specific permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a permission",
     *         @OA\JsonContent(ref="#/components/schemas/Permission")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show($id)
    {
        // Aktif kayıtları getirin
        $permission = Permission::where('id', $id)->where('status', 1)->first();
    
        if (!$permission) {
            return response()->json(['message' => 'Permission not found or has been deleted'], 404);
        }
    
        return response()->json($permission);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     summary="Create a new permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Permission")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Permission created",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            "status" => true,
            "message" => "Permission created successfully"
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/permissions/{id}",
     *     summary="Update an existing permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Permission")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->name = $request->name;
        $permission->save();
        return response()->json([
            "status" => true,
            "message" => "Permission updated successfully"
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/permissions/{id}",
     *     summary="Delete a permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Permission deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        $permission = Permission::where('status', 1)->find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }
        
        // Status'ü 0 (pasif) olarak güncelle
        $permission->status = 0;
        $permission->save();
        
        return response()->json(['message' => 'Permission deleted']);
    }
}
