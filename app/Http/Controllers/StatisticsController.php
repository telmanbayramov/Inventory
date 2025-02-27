<?php

namespace App\Http\Controllers;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Room;
use App\Models\Equipment;
use App\Models\Corp;
use App\Models\User;
use App\Models\Device;

class StatisticsController extends Controller
{
    /**
     * Get the count of all entities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facultyCount = Faculty::where('status','1')->count();

        $departmentCount = Department::where('status','1')->count();

        $roomCount = Room::where('status','1')->count();

        $deviceQuantitySum = Device::where('status', 1)->sum('quantity');

        $corpsCount = Corp::where('status','1')->count();

        $userCount = User::where('status','1')->count();

        return response()->json([
            'faculty_count' => $facultyCount,
            'department_count' => $departmentCount,
            'room_count' => $roomCount,
            'equipment_count' => $deviceQuantitySum,
            'corps_count' => $corpsCount,
            'user_count' => $userCount,
        ]);
    }
}
