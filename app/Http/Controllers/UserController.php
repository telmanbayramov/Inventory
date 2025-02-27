<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,App\Models\User')->only(['store']);
        $this->middleware('can:update,App\Models\User')->only(['update']);
        $this->middleware('can:delete,App\Models\User')->only(['destroy']);
        $this->middleware('can:views,App\Models\User')->only(['index']);
        $this->middleware('can:view,App\Models\User')->only(['show']);
    }

    public function index()
    {
        $users = User::
            where('status', 1)
            ->get();

        $users = $users->map(function ($user) {
            $response = [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'duty' => $user->duty,
                'employee_type' => $user->employee_type,
                'patronymic' => $user->patronymic,
                'roles' => $user->roles->pluck('id', 'name'), // Roller
            ];

            // if ($user->departments->isNotEmpty()) {
            //     $response['department_names'] = $user->departments->pluck('id', 'name'); // Departman isimleri
            // }


            // if ($user->faculty) {
            //     $response['faculty'] = [
            //         'id' => $user->faculty->id,
            //         'name' => $user->faculty->name,
            //     ];
            // }

            return $response;
        });

        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'departments', 'faculty'])->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'duty' => $user->duty,
            'employee_type' => $user->employee_type,
            'patronymic' => $user->patronymic,
            'roles' => $user->roles->pluck('id', 'name'), // Roller
        ];

        if ($user->departments->isNotEmpty()) {
            $response['department_names'] = $user->departments->pluck('id', 'name'); // Departman isimleri
        }

        // Fakülte varsa ekle
        if ($user->faculty) {
            $response['faculty'] = [
                'id' => $user->faculty->id,
                'name' => $user->faculty->name,
            ];
        }

        return response()->json($response);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'duty' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
            'patronymic' => 'required|string|max:255',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'role_id' => 'required|exists:roles,id', // Spatie üzerinden role id
        ]);

        // Kullanıcıyı oluştur
        $user = new User();
        $user->name = $validated['name'];
        $user->surname = $validated['surname'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->duty = $validated['duty'];
        $user->employee_type = $validated['employee_type'];
        $user->patronymic = $validated['patronymic'];
        $user->faculty_id = $validated['faculty_id'] ?? null;
        $user->save();
        // Kullanıcıya rol atama (Spatie)
        $role = Role::find($validated['role_id']);
        if ($role) {
            $user->syncRoles([$role->name]); // Spatie'nin `syncRoles` metodu
        }

        // Departmanları pivot tabloya ekle
        if (!empty($validated['department_ids'])) {
            $user->departments()->sync($validated['department_ids']);
        }

        return response()->json([
            'status' => true,
            'message' => 'User created and role assigned successfully'
        ], 201);
    }



    public function update(Request $request, $id)
    {
        $user = User::where('status', 1)->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6', // Parola isteğe bağlı hale getirildi
            'duty' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
            'patronymic' => 'required|string|max:255',
            'department_ids' => 'nullable|array', // Departmanlar bir dizi olarak gönderilecek
            'department_ids.*' => 'exists:departments,id', // Her eleman bir departman ID olmalı
            'faculty_id' => 'nullable|exists:faculties,id',
            'role_id' => 'nullable|exists:roles,id', // Rol ID isteğe bağlı olarak gönderilecek
        ]);

        // Kullanıcı bilgilerini güncelle
        $user->name = $validated['name'];
        $user->surname = $validated['surname'];
        $user->email = $validated['email'];

        if (isset($validated['password'])) {
            $user->password = bcrypt($validated['password']); // Parola şifreleme
        }

        $user->duty = $validated['duty'];
        $user->employee_type = $validated['employee_type'];
        $user->patronymic = $validated['patronymic'];
        if ($validated['faculty_id']) {
            $user->faculty_id = $validated['faculty_id'] ?? $user->faculty_id;
        }
        $user->save();

        // Departmanları pivot tabloya güncelle
        if (isset($validated['department_ids'])) {
            $user->departments()->sync($validated['department_ids']); // Mevcut departman ilişkilerini günceller
        }

        // Rolü güncelle (Spatie)
        if (isset($validated['role_id'])) {
            $role = Role::where('status', 1)->find($validated['role_id']);
            if ($role) {
                $user->syncRoles([$role->name]); // Kullanıcıya sadece yeni rol atanır
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully'
        ]);
    }


    public function destroy($id)
    {
        // Kullanıcıyı veritabanında ara
        $user = User::where('status', 1)->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Soft delete işlemi: status alanını 0 yaparak silme işlemi gerçekleştir
        $user->status = 0; // status 0, silindi olarak işaret edilecek
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User successfully deleted'
        ]);
    }
}
