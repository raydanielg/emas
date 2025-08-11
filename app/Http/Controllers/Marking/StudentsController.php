<?php

namespace App\Http\Controllers\Marking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentsController extends Controller
{
    // GET /marking/students
    public function index(Request $request)
    {
        $userId = Auth::id();
        // Get schools assigned to current user
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->pluck('school_id')
            ->unique()
            ->values();

        if ($assignedSchoolIds->isEmpty()) {
            // No schools assigned; show empty state
            $students = collect();
            $schools = collect();
            return view('marking.students', [
                'students' => $students,
                'schools' => $schools,
                'filters' => [
                    'school_id' => null,
                    'form' => null,
                    'q' => null,
                ],
                'pagination' => null,
            ]);
        }

        // Filters
        $schoolId = $request->query('school_id');
        $form = $request->query('form');
        $q = trim((string)$request->query('q'));

        // Schools list for filter
        $schools = DB::table('schools')
            ->select('id', 'name')
            ->whereIn('id', $assignedSchoolIds)
            ->orderBy('name')
            ->get();

        $query = DB::table('students as s')
            ->select('s.id','s.school_id','s.first_name','s.last_name','s.sex','s.form','s.exam_number')
            ->whereIn('s.school_id', $assignedSchoolIds);

        if ($schoolId && in_array((int)$schoolId, $assignedSchoolIds->all(), true)) {
            $query->where('s.school_id', (int)$schoolId);
        }
        if ($form) {
            $query->where('s.form', $form);
        }
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('s.first_name', 'like', "%$q%")
                    ->orWhere('s.last_name', 'like', "%$q%")
                    ->orWhere('s.exam_number', 'like', "%$q%");
            });
        }

        $perPage = 25;
        $page = (int) max(1, (int)$request->query('page', 1));

        // Clone for total
        $total = (clone $query)->count();
        $rows = $query
            ->orderBy('s.last_name')
            ->orderBy('s.first_name')
            ->forPage($page, $perPage)
            ->get();

        // Simple manual paginator structure for Blade (avoid dependency on models)
        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];

        return view('marking.students', [
            'students' => $rows,
            'schools' => $schools,
            'filters' => [
                'school_id' => $schoolId,
                'form' => $form,
                'q' => $q,
            ],
            'pagination' => $pagination,
        ]);
    }

    // GET /marking/students/{id}
    public function show($id)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->pluck('school_id')
            ->unique()
            ->values();

        $student = DB::table('students as s')
            ->join('schools as sch', 'sch.id', '=', 's.school_id')
            ->where('s.id', $id)
            ->select('s.*', 'sch.name as school_name')
            ->first();

        if (!$student || !$assignedSchoolIds->contains($student->school_id)) {
            throw new HttpException(403, 'Forbidden');
        }

        // Fetch marks for student with subject names (read-only)
        $marks = DB::table('marks as m')
            ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.entered_by')
            ->where('m.student_id', $student->id)
            ->orderBy('sub.name')
            ->select('m.id','m.form','m.score','sub.code as subject_code','sub.name as subject_name','u.name as entered_by')
            ->get();

        return view('marking.student_show', [
            'student' => $student,
            'marks' => $marks,
        ]);
    }
}
