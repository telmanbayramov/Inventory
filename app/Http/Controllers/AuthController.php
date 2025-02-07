<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Auth"
 * )
 */

class AuthController extends Controller
{

 
    public function index()
    {
        return User::all();
    }
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     description="Authenticate user and return a token",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="P@ssw0rd1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsInN1YiI6InVzZXIifQ.eyJleHBpcmVkX3ZhbHVlcyI6IkFjY291bnRfaGFzX3ZhbHVlIn0.Iywwu5r-9ctb65sds6apEYIRtqU_8n0MG_M6Ll72gX5o")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid details")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Gelen veriyi doğrula
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // JWTAuth kullanarak giriş kontrolü yap
        $credentials = $request->only('email', 'password');

        // Kullanıcı durumu kontrolü
        $user = User::where('email', $request->email)->where('status', 1)->first();

        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User not found or inactive"
            ], 401);
        }

        // Token oluştur ve şifre kontrolü yap
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid password"
            ], 401);
        }

        return response()->json([
            "status" => true,
            "message" => "User logged in successfully",
            "token" => $token
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     description="Returns user profile data",
     *     operationId="getUserProfile",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile data"),
     *             @OA\Property(property="userData", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function profile()
    {
        $userdata = auth()->user();
        $roles = $userdata->roles->pluck('name');
        $permissions = $userdata->getAllPermissions()->pluck('name');
        if ($userdata->status != 0) {
            return response()->json([
                "status" => true,
                "message" => "Profile data",
                "userData" => [
                    "id" => $userdata->id,
                    "name" => $userdata->name,
                    "surname" => $userdata->surname,
                    "email" => $userdata->email,
                    "roles" => $roles,
                    "permissions" => $permissions,
                ]
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "User account is inactive"
            ], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="This endpoint allows you to register a new user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "surname", "email", "password", "duty", "employee_type"},
     *             @OA\Property(property="name", type="string", example="John", description="User's first name"),
     *             @OA\Property(property="surname", type="string", example="Doe", description="User's last name"),
     *             @OA\Property(property="patronymic", type="string", example="Smith", description="User's patronymic name (optional)"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", example="StrongPassword123", description="User's password (minimum 8 characters)"),
     *             @OA\Property(property="duty", type="string", example="Manager", description="User's duty/role"),
     *             @OA\Property(property="employee_type", type="string", example="Full-time", description="Type of employment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "surname" => "required|string|max:255",
            "patronymic" => "nullable|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8",
            "duty" => "required|string|max:255",
            "employee_type" => "required|string|max:255"
        ]);

        // $user = User::create([
        //     "name" => $request->name,
        //     "surname" => $request->surname,
        //     "patronymic" => $request->patronymic,
        //     "email" => $request->email,
        //     "password" => Hash::make($request->password),
        //     "duty" => $request->duty,
        //     "employee_type" => $request->employee_type
        // ]);

        return response()->json([
            "status" => true,
            "message" => "User registered successfully",
            "user" => Hash::make($request->password)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
