<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Group;
use App\Models\Corp;
use App\Models\Room;
use App\Models\Lesson_Type;
use App\Models\Hour;
use App\Models\Semester;
use App\Models\Week_Type;
use App\Models\Day;
use App\Models\User;
use App\Models\Discipline;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Giriş yapan kullanıcıyı al
        $result = [];
    
        // Superadmin → Tüm fakülteleri görebilir
        if ($user->can('view_all_faculties')) {
            $faculties = Faculty::where('status', 1)->get();
        }
        // Fakülte Admini → Sadece kendi fakültesini görebilir
        elseif ($user->can('view_own_faculty')) {
            $faculties = Faculty::where('id', $user->faculty_id)->where('status', 1)->get();
        }
        // Öğretmen → Sadece kendi derslerini görebilir
        elseif ($user->can('view_own_lessons')) {
            $facultyIds = Schedule::where('user_id', $user->id)
                ->where('status', 1)
                ->pluck('faculty_id') // Burada doğrudan schedule.faculty_id alıyoruz
                ->unique()
                ->toArray();
    
            $faculties = Faculty::whereIn('id', $facultyIds)->where('status', 1)->get();
        } else {
            return response()->json(['message' => 'Yetkisiz erişim'], 403);
        }
    
        foreach ($faculties as $faculty) {
            $scheduleQuery = Schedule::with('group')
                ->where('faculty_id', $faculty->id)
                ->where('status', 1);
    
            // Fakülte admini sadece kendi eklediği dersleri görmeli
            if ($user->can('view_own_lessons')) {
                $scheduleQuery->where('user_id', $user->id);
            }
    
            $schedules = $scheduleQuery->get();
    
            // Bu döngüde, eğer grup başka bir fakülteye aitse, onu o fakülte altına ekliyoruz
            $facultyLessons = [];
            foreach ($schedules as $schedule) {
                $groupFacultyId = $schedule->group->faculty_id; // Grup fakülte ID'sini alıyoruz
                $targetFacultyId = $faculty->id; // Mevcut fakülte ID'si
    
                // Eğer grup başka fakültedense, doğru fakülte altında listeye ekliyoruz
                if ($groupFacultyId !== $targetFacultyId) {
                    // Bu durumda, doğru fakülte altına ders ekleyeceğiz
                    $targetFacultyId = $groupFacultyId;
                }
    
                $facultyLessons[$targetFacultyId][] = [
                    'schedule_id' => $schedule->id,
                    'day_name' => optional($schedule->day)->name,
                    'hour_name' => optional($schedule->hour)->name,
                    'discipline_name' => optional($schedule->discipline)->name,
                    'user_name' => optional($schedule->user)->name,
                    'corp_name' => optional($schedule->corp)->name,
                    'lesson_type_name' => optional($schedule->lessonType)->name,
                    'room_name' => optional($schedule->room)->name,
                    'year' => optional($schedule->semester)->year,
                    'semester_num' => optional($schedule->semester)->semester_num,
                    'week_type_name' => optional($schedule->weekType)->name,
                    'group_name' => optional($schedule->group)->name,
                ];
            }
    
            // Fakülteyi ve dersleri ekle
            foreach ($facultyLessons as $facultyId => $lessons) {
                $result[] = [
                    'faculty_name' => Faculty::find($facultyId)->name,
                    'faculty_id' => $facultyId,
                    'lessons' => $lessons,
                ];
            }
        }
    
        return response()->json(['faculties' => $result]);
    }
    
    public function test(Request $request)
    {
        // Filtreleme kriterlerini dinamik olarak al ve varsa filtre uygula
        $filters = [
            'faculty_id' => $request->query('faculty_id'),
            'department_id' => $request->query('department_id'),
            'group_id' => $request->query('group_id'),
            'corp_id' => $request->query('corp_id'),
            'room_id' => $request->query('room_id'),
            'lesson_type_id' => $request->query('lesson_type_id'),
            'hour_id' => $request->query('hour_id'),
            'semester_id' => $request->query('semester_id'),
            'week_type_id' => $request->query('week_type_id'),
            'day_id' => $request->query('day_id'),
            'user_id' => $request->query('user_id'),
            'discipline_id' => $request->query('discipline_id'),
            'confirm_status' => $request->query('confirm_status'),
        ];

        $schedulesQuery = Schedule::where('status', '1')
            ->with([
                'faculty',
                'department',
                'group',
                'corp',
                'room',
                'lessonType',
                'hour',
                'semester',
                'weekType',
                'day',
                'user',
                'discipline',
            ]);

        foreach ($filters as $column => $value) {
            if ($value !== null) {
                $schedulesQuery->where($column, $value);
            }
        }

        $schedules = $schedulesQuery->get();
        $groupedSchedules = $schedules->groupBy('group_id')->map(function ($groupSchedules) {
            $firstSchedule = $groupSchedules->first();

            return [
                'id' => $firstSchedule->id,
                'group_name' => optional($firstSchedule->group)->name,
                'faculty_name' => optional($firstSchedule->faculty)->name,
                'department_name' => optional($firstSchedule->department)->name,
                'confirm_status' => $firstSchedule->confirm_status,
                'lessons' => $groupSchedules->map(function ($schedule) {
                    return [
                        'schedule_id' => $schedule->id,
                        'day_name' => optional($schedule->day)->name,
                        'hour_name' => optional($schedule->hour)->name,
                        'discipline_name' => optional($schedule->discipline)->name,
                        'user_name' => optional($schedule->user)->name,
                        'corp_name' => optional($schedule->corp)->name,
                        'lesson_type_name' => optional($schedule->lessonType)->name,
                        'room_name' => optional($schedule->room)->name,
                        'year' => optional($schedule->semester)->year,
                        'semester_num' => optional($schedule->semester)->semester_num,
                        'week_type_name' => optional($schedule->weekType)->name,
                    ];
                }),
            ];
        });

        return response()->json(['schedules' => $groupedSchedules]);
    }


    public function show($id)
    {
        $user = auth()->user();

        // Super Admin tüm programları görebilir
        if ($user->can('view_all_schedules')) {
            $schedule = Schedule::where('status', 1)->with([
                'faculty',
                'department',
                'group',
                'corp',
                'room',
                'lessonType',
                'hour',
                'semester',
                'weekType',
                'day',
                'user',
                'discipline',
            ])->find($id);
        }
        // Fakülte admini sadece kendi fakültesine ait programları görebilir
        elseif ($user->can('view_faculty_schedules')) {
            $schedule = Schedule::where('status', 1)
                ->where('faculty_id', $user->faculty_id)
                ->with([
                    'faculty',
                    'department',
                    'group',
                    'corp',
                    'room',
                    'lessonType',
                    'hour',
                    'semester',
                    'weekType',
                    'day',
                    'user',
                    'discipline',
                ])->find($id);
        }
        // Öğretmen yalnızca kendi verdiği derslerin programını görebilir
        elseif ($user->can('view_own_lessons')) {
            $schedule = Schedule::where('status', 1)
                ->where('user_id', $user->id)
                ->with([
                    'faculty',
                    'department',
                    'group',
                    'corp',
                    'room',
                    'lessonType',
                    'hour',
                    'semester',
                    'weekType',
                    'day',
                    'user',
                    'discipline',
                ])->find($id);
        }
        // Yetkisi olmayan kullanıcılar erişemez
        else {
            return response()->json(['message' => 'Yetkisiz erişim'], 403);
        }

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $formattedSchedule = [
            'id' => $schedule->id,
            'faculty_name' => optional($schedule->faculty)->name,
            'faculty_id' => optional($schedule->faculty)->id,
            'department_name' => optional($schedule->department)->name,
            'department_id' => optional($schedule->department)->id,
            'group_name' => optional($schedule->group)->name,
            'group_id' => optional($schedule->group)->id,
            'corp_name' => optional($schedule->corp)->name,
            'corp_id' => optional($schedule->corp)->id,
            'room_name' => optional($schedule->room)->name,
            'room_id' => optional($schedule->room)->id,
            'lesson_type_name' => optional($schedule->lessonType)->name,
            'lesson_type_id' => optional($schedule->lessonType)->id,
            'hour_name' => optional($schedule->hour)->name,
            'hour_id' => optional($schedule->hour)->id,
            'year' => optional($schedule->semester)->year,
            'semester_num' => optional($schedule->semester)->semester_num,
            'semester_id' => optional($schedule->semester)->id,
            'week_type_name' => optional($schedule->weekType)->name,
            'week_type_id' => optional($schedule->weekType)->id,
            'day_name' => optional($schedule->day)->name,
            'day_id' => optional($schedule->day)->id,
            'user_name' => optional($schedule->user)->name,
            'user_id' => optional($schedule->user)->id,
            'discipline_name' => optional($schedule->discipline)->name,
            'discipline_id' => optional($schedule->discipline)->id,
        ];

        return response()->json(['schedule' => $formattedSchedule]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|integer|exists:faculties,id',
            'department_id' => 'required|integer|exists:departments,id',
            'group_id' => 'required|array',
            'group_id.*' => 'integer|exists:groups,id',
            'corp_id' => 'required|integer|exists:corps,id',
            'room_id' => 'required|integer|exists:rooms,id',
            'lesson_type_id' => 'required|integer|exists:lesson_types,id',
            'hour_id' => 'required|integer|exists:hours,id',
            'semester_id' => 'required|integer|exists:semesters,id',
            'week_type_id' => 'required|integer|exists:week_types,id',
            'day_id' => 'required|integer|exists:days,id',
            'user_id' => 'required|integer|exists:users,id',
            'discipline_id' => 'required|integer|exists:disciplines,id',
        ]);

        // Kullanıcının aynı semester, hafta, gün ve saatte dersi var mı?
        $existingUserSchedule = Schedule::where('user_id', $validated['user_id'])
            ->where('hour_id', $validated['hour_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('week_type_id', $validated['week_type_id'])
            ->where('day_id', $validated['day_id'])
            ->get();

        foreach ($existingUserSchedule as $schedule) {
            // Eğer aynı derse aitse ve muhazire (lesson_type_id = 2) ise farklı odalara izin verme
            if (
                $schedule->lesson_type_id == 2 &&
                $schedule->room_id != $validated['room_id']
            ) {
                return response()->json([
                    'message' => 'Eyni semester, həftə, gün və saatda fərqli otaqda muhazire əlavə edə bilməzsiniz.'
                ], 403);
            }
        }

        // Oda doluluk kontrolü
        $roomControl = Schedule::where('room_id', $validated['room_id'])
            ->where('hour_id', $validated['hour_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('week_type_id', $validated['week_type_id'])
            ->where('day_id', $validated['day_id'])
            ->get();

        foreach ($roomControl as $schedule) {
            // Eğer ders muhazire (lesson_type_id = 2) değilse ve aynı user tarafından eklenmemişse izin verme
            if (
                $schedule->lesson_type_id != 2 &&  // Ders muhazire değilse
                $schedule->user_id != $validated['user_id'] // Farklı bir kullanıcı eklemişse
            ) {
                return response()->json([
                    'message' => 'Seçilmiş otaq eyni semester, həftə, gün və saat üçün artıq doludur.'
                ], 403);
            }
        }

        $room = Room::findOrFail($validated['room_id']);
        $confirmStatus = $room->room_type_id == 2 ? 0 : 1; // Oda türüne göre onay durumu belirleniyor.

        // Schedule verilerinin topluca hazırlanması
        $schedulesData = collect($validated['group_id'])->map(function ($groupId) use ($validated, $confirmStatus) {
            return [
                'faculty_id' => $validated['faculty_id'],
                'department_id' => $validated['department_id'],
                'group_id' => $groupId,
                'corp_id' => $validated['corp_id'],
                'room_id' => $validated['room_id'],
                'lesson_type_id' => $validated['lesson_type_id'],
                'hour_id' => $validated['hour_id'],
                'semester_id' => $validated['semester_id'],
                'week_type_id' => $validated['week_type_id'],
                'day_id' => $validated['day_id'],
                'user_id' => $validated['user_id'],
                'discipline_id' => $validated['discipline_id'],
                'status' => 1, // Varsayılan olarak aktif.
                'confirm_status' => $confirmStatus, // Onay durumu.
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Tüm verilerin topluca veritabanına yazılması
        Schedule::insert($schedulesData->toArray());

        return response()->json([
            'message' => 'Schedules created successfully and pending approval.',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validated = $request->validate([
            'faculty_id' => 'required|integer|exists:faculties,id',
            'department_id' => 'required|integer|exists:departments,id',
            'group_id' => 'required|integer|exists:groups,id', // Artık array değil
            'corp_id' => 'required|integer|exists:corps,id',
            'room_id' => 'required|integer|exists:rooms,id',
            'lesson_type_id' => 'required|integer|exists:lesson_types,id',
            'hour_id' => 'required|integer|exists:hours,id',
            'semester_id' => 'required|integer|exists:semesters,id',
            'week_type_id' => 'required|integer|exists:week_types,id',
            'day_id' => 'required|integer|exists:days,id',
            'user_id' => 'required|integer|exists:users,id',
            'discipline_id' => 'required|integer|exists:disciplines,id',
        ]);

        // Güncellenecek Schedule kaydını bul
        $schedule = Schedule::findOrFail($id);

        // Oda kontrolü (çakışma kontrolü)
        $roomConflict = Schedule::where('room_id', $validated['room_id'])
            ->where('hour_id', $validated['hour_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('week_type_id', $validated['week_type_id'])
            ->where('day_id', $validated['day_id'])
            ->where('id', '!=', $schedule->id) // Kendisi hariç
            ->exists();

        if ($roomConflict) {
            return response()->json([
                'message' => 'Daxil etdiyiniz parametrlərlə seçdiyiniz otaq doludur',
            ], 403);
        }

        // Oda türüne göre confirm_status belirle
        $room = Room::findOrFail($validated['room_id']);
        $confirmStatus = $room->room_type_id == 2 ? 0 : 1;

        // Güncelleme işlemi
        $schedule->update([
            'faculty_id' => $validated['faculty_id'],
            'department_id' => $validated['department_id'],
            'group_id' => $validated['group_id'], // Direkt tekil ID atanıyor
            'corp_id' => $validated['corp_id'],
            'room_id' => $validated['room_id'],
            'lesson_type_id' => $validated['lesson_type_id'],
            'hour_id' => $validated['hour_id'],
            'semester_id' => $validated['semester_id'],
            'week_type_id' => $validated['week_type_id'],
            'day_id' => $validated['day_id'],
            'user_id' => $validated['user_id'],
            'discipline_id' => $validated['discipline_id'],
            'confirm_status' => $confirmStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Schedule updated successfully.',
        ], 200);
    }


    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update(['status' => '0']);
        return response()->json(['message' => 'Schedule deleted successfully']);
    }


    public function getDepartmentsByFaculty($faculty_id)
    {
        $faculty = Faculty::where('status', '1')->find($faculty_id);
        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }
        $departments = $faculty->departments()->where('status', 1)->get();
        return response()->json($departments);
    }
    public function getGroupsByFaculty($faculty_id)
    {
        $faculty = Faculty::where('status', '1')->find($faculty_id);

        if (!$faculty) {
            return response()->json(['message' => 'Faculty not found'], 404);
        }

        $groups = $faculty->groups()->where('status', '1')->get();

        return response()->json($groups);
    }
    public function getDisciplinesByDepartment($department_id)
    {
        $department = Department::where('status', '1')->find($department_id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $disciplines = $department->disciplines()->where('status', 1)->get();

        return response()->json($disciplines);
    }
    public function getUsersByDepartment($department_id)
    {
        $department = Department::where('status', '1')->find($department_id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $users = $department->users()->where('status', 1)->get();

        return response()->json($users);
    }
    public function filterSchedules(Request $request)
    {
        $facultyId = $request->input('faculty_id');
        $schedulesQuery = Schedule::where('status', '1');

        if ($facultyId) {
            $schedulesQuery->where('faculty_id', $facultyId);
        }

        $schedules = $schedulesQuery->with([
            'faculty',
            'department',
            'group',
            'corp',
            'room',
            'lessonType',
            'hour',
            'semester',
            'weekType',
            'day',
            'user',
            'discipline',
        ])->get();

        return response()->json(['schedules' => $schedules]);
    }
}
