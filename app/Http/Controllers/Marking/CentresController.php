<?php

namespace App\Http\Controllers\Marking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CentresController extends Controller
{
    // GET /marking/centres
    public function index(Request $request)
    {
        $userId = Auth::id();
        $q = trim((string) $request->query('q'));

        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->pluck('school_id');

        $query = DB::table('schools as s')
            ->join('wards as w', 'w.id', '=', 's.ward_id')
            ->join('districts as d', 'd.id', '=', 'w.district_id')
            ->join('regions as r', 'r.id', '=', 'd.region_id')
            ->whereIn('s.id', $assignedSchoolIds)
            ->select('s.id','s.code','s.name','w.name as ward','d.name as district','r.name as region');

        if ($q !== '') {
            $query->where(function($sub) use ($q){
                $sub->where('s.name','like',"%$q%")
                    ->orWhere('s.code','like',"%$q%")
                    ->orWhere('d.name','like',"%$q%")
                    ->orWhere('r.name','like',"%$q%");
            });
        }

        $schools = $query->orderBy('s.name')->get();

        return view('marking.centres', [
            'schools' => $schools,
            'q' => $q,
        ]);
    }

    // GET /marking/centres/{school}/sheet
    public function sheet($schoolId)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->pluck('school_id');

        $school = DB::table('schools')->where('id', $schoolId)->first();
        if (!$school || !$assignedSchoolIds->contains((int)$schoolId)) {
            throw new HttpException(403, 'Forbidden');
        }

        // Subjects
        $subjects = DB::table('subjects')->orderBy('name')->get();

        // Students in school
        $students = DB::table('students')
            ->where('school_id', $schoolId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->select('id','first_name','last_name','sex','form','exam_number')
            ->get();

        // Existing marks
        $marks = DB::table('marks')
            ->where('school_id', $schoolId)
            ->select('student_id','subject_id','score')
            ->get();
        // Map marks: [student_id][subject_id] => score
        $markMap = [];
        foreach ($marks as $m) {
            $markMap[$m->student_id][$m->subject_id] = $m->score;
        }

        // Editable subjects for this user at this school (limit to ONE active subject)
        $editableAll = DB::table('user_subject_assignments as usa')
            ->where('usa.user_id', $userId)
            ->where('usa.school_id', $schoolId)
            ->pluck('usa.subject_id')
            ->toArray();
        $activeSubjectId = null; $activeSubjectName = null; $editable = [];
        if (!empty($editableAll)) {
            $activeSubjectId = (int) $editableAll[0];
            $editable = [$activeSubjectId];
            $activeSubjectName = DB::table('subjects')->where('id',$activeSubjectId)->value('name');
        }

        return view('marking.centre_sheet', [
            'school' => $school,
            'students' => $students,
            'subjects' => $subjects,
            'marks' => $markMap,
            'editable_subject_ids' => $editable,
            'active_subject_id' => $activeSubjectId,
            'active_subject_name' => $activeSubjectName,
        ]);
    }
}
