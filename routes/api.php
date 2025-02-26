<?php

use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CorpController;
use App\Http\Controllers\Room_TypeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HourController;
use App\Http\Controllers\LessonTypeController;
use App\Http\Controllers\LessonTypeHourController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomPermissionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Week_TypeController;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/users', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group([
    "middleware" => ["auth:api"]
], function () {

    Route::get("profile", [AuthController::class, "profile"]);

    
    Route::get("permissions", [PermissionController::class, "index"])->middleware('can:views,App\Models\Permission');
    Route::post("permissions", [PermissionController::class, "store"])->middleware('can:create,App\Models\Permission');
    Route::put("permissions/{id}", [PermissionController::class, "update"])->middleware('can:update,App\Models\Permission');
    Route::get("permissions/{id}", [PermissionController::class, "show"])->middleware('can:view,App\Models\Permission');
    Route::delete("permissions/{id}", [PermissionController::class, "destroy"])->middleware('can:delete,App\Models\Permission');

    
    Route::get("roles", [RoleController::class, "index"])->middleware('can:views,App\Models\Role');
    Route::post("roles", [RoleController::class, "store"])->middleware('can:create,App\Models\Role');
    Route::put("roles/{id}", [RoleController::class, "update"])->middleware('can:update,App\Models\Role');
    Route::get("roles/{id}", [RoleController::class, "show"])->middleware('can:view,App\Models\Role');
    Route::delete("roles/{id}", [RoleController::class, "destroy"])->middleware('can:delete,App\Models\Role');

    Route::get("users", [UserController::class, "index"])->middleware('can:views,App\Models\User');
    Route::post("users", [UserController::class, "store"])->middleware('can:create,App\Models\User');
    Route::put("users/{id}", [UserController::class, "update"])->middleware('can:update,App\Models\User');
    Route::get("users/{id}", [UserController::class, "show"])->middleware('can:view,App\Models\User');
    Route::delete("users/{id}", [UserController::class, "destroy"])->middleware('can:delete,App\Models\User');

    //faculties proccess
    Route::get("faculties", [FacultyController::class, "index"])->middleware('can:views,App\Models\Faculty');
    Route::post("faculties", [FacultyController::class, "store"])->middleware('can:create,App\Models\Faculty');
    Route::put("faculties/{id}", [FacultyController::class, "update"])->middleware('can:update,App\Models\Faculty');
    Route::get("faculties/{id}", [FacultyController::class, "show"])->middleware('can:view,App\Models\Faculty');
    Route::delete("faculties/{id}", [FacultyController::class, "destroy"])->middleware('can:delete,App\Models\Faculty');

    //departments proccess
    Route::get("departments", [DepartmentController::class, "index"])->middleware('can:views,App\Models\Department');
    Route::post("departments", [DepartmentController::class, "store"])->middleware('can:create,App\Models\Department');
    Route::put("departments/{id}", [DepartmentController::class, "update"])->middleware('can:update,App\Models\Department');
    Route::get("departments/{id}", [DepartmentController::class, "show"])->middleware('can:view,App\Models\Department');
    Route::delete("departments/{id}", [DepartmentController::class, "destroy"])->middleware('can:delete,App\Models\Department');



    //lesson_types proccess
    Route::get("lesson_types", [LessonTypeController::class, "index"])->middleware('can:views,App\Models\Lesson_Type');
    Route::post("lesson_types", [LessonTypeController::class, "store"])->middleware('can:create,App\Models\Lesson_Type');
    Route::put("lesson_types/{id}", [LessonTypeController::class, "update"])->middleware('can:update,App\Models\Lesson_Type');
    Route::get("lesson_types/{id}", [LessonTypeController::class, "show"])->middleware('can:view,App\Models\Lesson_Type');
    Route::delete("lesson_types/{id}", [LessonTypeController::class, "destroy"])->middleware('can:delete,App\Models\Lesson_Type');

    //corps proccess
    Route::get("corps", [CorpController::class, "index"])->middleware('can:views,App\Models\Corp');
    Route::post("corps", [CorpController::class, "store"])->middleware('can:create,App\Models\Corp');
    Route::put("corps/{id}", [CorpController::class, "update"])->middleware('can:update,App\Models\Corp');
    Route::get("corps/{id}", [CorpController::class, "show"])->middleware('can:view,App\Models\Corp');
    Route::delete("corps/{id}", [CorpController::class, "destroy"])->middleware('can:delete,App\Models\Corp');

    Route::get("room_types", [Room_TypeController::class, "index"])->middleware('can:views,App\Models\Room_Type');
    Route::post("room_types", [Room_TypeController::class, "store"])->middleware('can:create,App\Models\Room_Type');
    Route::put("room_types/{id}", [Room_TypeController::class, "update"])->middleware('can:update,App\Models\Room_Type');
    Route::get("room_types/{id}", [Room_TypeController::class, "show"])->middleware('can:view,App\Models\Room_Type');
    Route::delete("room_types/{id}", [Room_TypeController::class, "destroy"])->middleware('can:delete,App\Models\Room_Type');

    Route::get("rooms", [RoomController::class, "index"])->middleware('can:views,App\Models\Room');
    Route::post("rooms", [RoomController::class, "store"])->middleware('can:create,App\Models\Room');
    Route::put("rooms/{id}", [RoomController::class, "update"])->middleware('can:update,App\Models\Room');
    Route::get("rooms/{id}", [RoomController::class, "show"])->middleware('can:view,App\Models\Room');
    Route::delete("rooms/{id}", [RoomController::class, "destroy"])->middleware('can:delete,App\Models\Room');


    Route::get("equipments", [EquipmentController::class, "index"])->middleware('can:viewAny,App\Models\Equipment');
    Route::post("equipments",[EquipmentController::class, "store"])->middleware('can:create,App\Models\Equipment');
    Route::put("equipments/{id}",[EquipmentController::class, "update"])->middleware('can:update,App\Models\Equipment');
    Route::get("equipments/{id}",[EquipmentController::class, "show"])->middleware('can:view,App\Models\Equipment');
    Route::delete("equipments/{id}",[EquipmentController::class, "destroy"])->middleware('can:delete,App\Models\Equipment');
});
