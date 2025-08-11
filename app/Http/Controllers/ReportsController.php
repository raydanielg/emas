<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    // Schools registration summary for assigned schools
    public function registration(Request $request)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)->pluck('school_id');

        $schools = DB::table('schools as s')
            ->leftJoin('wards as w','w.id','=','s.ward_id')
            ->leftJoin('districts as d','d.id','=','w.district_id')
            ->leftJoin('regions as r','r.id','=','d.region_id')
            ->whereIn('s.id', $assignedSchoolIds)
            ->select('s.id','s.code','s.name','w.name as ward','d.name as district','r.name as region')
            ->orderBy('s.name')
            ->get();

        $counts = DB::table('students')
            ->select('school_id',
                DB::raw("SUM(CASE WHEN sex='F' THEN 1 ELSE 0 END) as female"),
                DB::raw("SUM(CASE WHEN sex='M' THEN 1 ELSE 0 END) as male"),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id')->pluck('total','school_id');

        $male = DB::table('students')
            ->select('school_id', DB::raw("SUM(CASE WHEN sex='M' THEN 1 ELSE 0 END) as m"))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id')->pluck('m','school_id');
        $female = DB::table('students')
            ->select('school_id', DB::raw("SUM(CASE WHEN sex='F' THEN 1 ELSE 0 END) as f"))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id')->pluck('f','school_id');

        return view('reports.registration', compact('schools','counts','male','female'));
    }

    // Progress report across subjects for assigned schools
    public function progress(Request $request)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)->pluck('school_id');

        // All subjects
        $subjects = DB::table('subjects')
            ->orderBy('name')
            ->get();

        $schools = DB::table('schools as s')
            ->leftJoin('wards as w','w.id','=','s.ward_id')
            ->leftJoin('districts as d','d.id','=','w.district_id')
            ->leftJoin('regions as r','r.id','=','d.region_id')
            ->whereIn('s.id', $assignedSchoolIds)
            ->select('s.id','s.code','s.name','d.name as district')
            ->orderBy('s.name')
            ->get();

        // Registered per school (total students per school)
        $registered = DB::table('students')
            ->select('school_id', DB::raw('COUNT(*) as c'))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id')->pluck('c','school_id');

        // Entered marks per school per subject
        $entered = DB::table('marks')
            ->select('school_id','subject_id', DB::raw('COUNT(*) as c'))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id','subject_id')->get();
        $enteredMap = [];
        foreach ($entered as $row) { $enteredMap[$row->school_id][$row->subject_id] = (int)$row->c; }

        // Per-subject totals across assigned schools
        $subjectTotals = [];
        foreach ($subjects as $sub) {
            $sid = $sub->id;
            $enteredTotal = 0; $registeredTotal = 0;
            foreach ($schools as $s) {
                $registeredTotal += (int)($registered[$s->id] ?? 0);
                $enteredTotal += (int)($enteredMap[$s->id][$sid] ?? 0);
            }
            $pct = $registeredTotal ? (int) round(($enteredTotal / $registeredTotal) * 100) : 0;
            $subjectTotals[$sid] = [
                'entered' => $enteredTotal,
                'registered' => $registeredTotal,
                'pct' => $pct,
                'name' => $sub->name,
            ];
        }

        // Overall totals across all subjects (exclude subjects with zero entered)
        $overall = ['entered' => 0, 'registered' => 0, 'pct' => 0];
        foreach ($subjectTotals as $st) {
            if (((int)($st['entered'] ?? 0)) <= 0) continue; // skip subjects not yet entered at all
            $overall['entered'] += (int)($st['entered'] ?? 0);
            $overall['registered'] += (int)($st['registered'] ?? 0);
        }
        $overall['pct'] = $overall['registered'] ? (int) round(($overall['entered'] / $overall['registered']) * 100) : 0;

        return view('reports.progress', compact('subjects','schools','registered','enteredMap','subjectTotals','overall'));
    }

    // Live API for Vue app
    public function progressApi(Request $request)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)->pluck('school_id');

        $subjects = DB::table('subjects')->orderBy('name')->get();

        $schools = DB::table('schools as s')
            ->leftJoin('wards as w','w.id','=','s.ward_id')
            ->leftJoin('districts as d','d.id','=','w.district_id')
            ->leftJoin('regions as r','r.id','=','d.region_id')
            ->whereIn('s.id', $assignedSchoolIds)
            ->select('s.id','s.code','s.name','d.name as district')
            ->orderBy('s.name')
            ->get();

        $registered = DB::table('students')
            ->select('school_id', DB::raw('COUNT(*) as c'))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id')->pluck('c','school_id');

        $entered = DB::table('marks')
            ->select('school_id','subject_id', DB::raw('COUNT(*) as c'))
            ->whereIn('school_id', $assignedSchoolIds)
            ->groupBy('school_id','subject_id')->get();
        $enteredMap = [];
        foreach ($entered as $row) { $enteredMap[$row->school_id][$row->subject_id] = (int)$row->c; }

        $subjectTotals = [];
        foreach ($subjects as $sub) {
            $sid = $sub->id;
            $enteredTotal = 0; $registeredTotal = 0;
            foreach ($schools as $s) {
                $registeredTotal += (int)($registered[$s->id] ?? 0);
                $enteredTotal += (int)($enteredMap[$s->id][$sid] ?? 0);
            }
            $pct = $registeredTotal ? (int) round(($enteredTotal / $registeredTotal) * 100) : 0;
            $subjectTotals[$sid] = [
                'entered' => $enteredTotal,
                'registered' => $registeredTotal,
                'pct' => $pct,
                'name' => $sub->name,
            ];
        }

        // Overall totals across all subjects (exclude subjects with zero entered)
        $overall = ['entered' => 0, 'registered' => 0, 'pct' => 0];
        foreach ($subjectTotals as $st) {
            if (((int)($st['entered'] ?? 0)) <= 0) continue;
            $overall['entered'] += (int)($st['entered'] ?? 0);
            $overall['registered'] += (int)($st['registered'] ?? 0);
        }
        $overall['pct'] = $overall['registered'] ? (int) round(($overall['entered'] / $overall['registered']) * 100) : 0;

        return response()->json([
            'subjects' => $subjects,
            'schools' => $schools,
            'registered' => (object)$registered,
            'enteredMap' => (object)$enteredMap,
            'subjectTotals' => (object)$subjectTotals,
            'overall' => $overall,
        ]);
    }
}
