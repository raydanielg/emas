<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $region = $request->query('region');
        $district = $request->query('district');
        $ward = $request->query('ward');
        $schoolId = $request->query('school');

        // User's assigned schools (and optional form restriction)
        $assigned = DB::table('user_school_assignments')
            ->where('user_id', $user->id)
            ->get(['school_id', 'form']);
        $assignedSchoolIds = $assigned->pluck('school_id')->unique()->values()->all();

        // Base schools query limited by user assignments and filters
        $schoolsQuery = DB::table('schools as s')
            ->join('wards as w', 'w.id', '=', 's.ward_id')
            ->join('districts as d', 'd.id', '=', 'w.district_id')
            ->join('regions as r', 'r.id', '=', 'd.region_id')
            ->select('s.id', 's.code', 's.name', 'w.name as ward_name', 'd.name as district_name', 'r.name as region_name');

        if (!empty($assignedSchoolIds)) {
            $schoolsQuery->whereIn('s.id', $assignedSchoolIds);
        } else {
            // If no explicit assignments, default to no access
            $schoolsQuery->whereRaw('1=0');
        }
        if ($region) { $schoolsQuery->where('r.name', $region); }
        if ($district) { $schoolsQuery->where('d.name', $district); }
        if ($ward) { $schoolsQuery->where('w.name', $ward); }
        if ($schoolId) { $schoolsQuery->where('s.id', (int)$schoolId); }

        $schools = $schoolsQuery->get();
        $schoolIds = $schools->pluck('id')->all();

        // Subjects
        $subjects = DB::table('subjects')->orderBy('id')->get(['id','name'])->map(fn($s)=>$s->name)->all();
        if (empty($subjects)) {
            $subjects = ['Civics','History','Geography','Kiswahili','English','Physics','Chemistry','Biology','Basic Math'];
        }

        // Students per school by sex
        $studentsAgg = DB::table('students')
            ->select('school_id', DB::raw("sum(case when sex='F' then 1 else 0 end) as female"), DB::raw("sum(case when sex='M' then 1 else 0 end) as male"), DB::raw('count(*) as total'))
            ->whereIn('school_id', $schoolIds)
            ->groupBy('school_id')
            ->get()
            ->keyBy('school_id');

        // Total students across visible schools
        $totalStudents = (int) ($studentsAgg->sum('total'));

        // Entries entered by user (distinct student) across visible schools
        $enteredByYouDistinctStudents = DB::table('marks')
            ->where('entered_by', $user->id)
            ->whereIn('school_id', $schoolIds)
            ->distinct('student_id')
            ->count('student_id');

        $remainder = max(0, $totalStudents - $enteredByYouDistinctStudents);

        // Progress per school per subject
        // For percentage: (marks count for subject & school)/(students in school) * 100
        $progress = [];
        if (!empty($schoolIds)) {
            // Count marks per school and subject
            $markCounts = DB::table('marks')
                ->select('school_id', 'subject_id', DB::raw('count(*) as c'))
                ->whereIn('school_id', $schoolIds)
                ->groupBy('school_id', 'subject_id')
                ->get();

            // Map subject_id to subject name (fallback mapping by index if subjects table empty)
            $subjectIdName = DB::table('subjects')->pluck('name', 'id');

            foreach ($schools as $s) {
                $female = (int) ($studentsAgg[$s->id]->female ?? 0);
                $male = (int) ($studentsAgg[$s->id]->male ?? 0);
                $total = max(1, (int) ($studentsAgg[$s->id]->total ?? 0));
                $perSubj = [];

                if ($subjectIdName->isNotEmpty()) {
                    foreach ($subjectIdName as $sid => $sname) {
                        $count = (int) ($markCounts->firstWhere(fn($m)=>$m->school_id==$s->id && $m->subject_id==$sid)->c ?? 0);
                        $perSubj[$sname] = (int) round(($count / $total) * 100);
                    }
                } else {
                    // No subjects table data: use generic subjects with zeros
                    foreach ($subjects as $sname) { $perSubj[$sname] = 0; }
                }

                $progress[] = [
                    'id' => $s->id,
                    'name' => $s->name,
                    'female' => $female,
                    'male' => $male,
                    'total' => $female + $male,
                    'progress' => $perSubj,
                ];
            }
        }

        // Completed schools: all subjects >= 100
        $completed = array_values(array_filter($progress, function ($row) use ($subjects) {
            foreach ($subjects as $subj) {
                if (($row['progress'][$subj] ?? 0) < 100) return false;
            }
            return true;
        }));

        // Pass rate per school = avg of subject percentages (naive demo metric)
        $passRates = [];
        foreach ($progress as $row) {
            $vals = array_map(fn($s)=> $row['progress'][$s] ?? 0, $subjects);
            $avg = count($vals) ? (int) round(array_sum($vals)/count($vals)) : 0;
            $passRates[] = ['name' => $row['name'], 'pass' => min(100, max(0, $avg))];
        }

        return response()->json([
            'filters' => [
                'region' => $region,
                'district' => $district,
                'ward' => $ward,
                'school' => $schoolId,
            ],
            'subjects' => $subjects,
            'centres' => $progress,
            'kpis' => [
                'assigned_centres' => count($schoolIds),
                'total_students' => $totalStudents,
                'entries_by_you' => (int) $enteredByYouDistinctStudents,
                'remainder' => (int) $remainder,
            ],
            'completed_schools' => array_map(fn($r)=> $r['name'], $completed),
            'pass_rates' => $passRates,
            'recent_activity' => DB::table('activity_logs')->where('user_id', $user->id)->latest()->limit(10)->get(['action','meta','created_at']),
        ]);
    }
}
