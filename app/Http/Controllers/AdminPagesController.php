<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AdminPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $role = strtolower((string)($user->role ?? ''));
            if (!in_array($role, ['admin','superadmin'])) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'users' => null,
            'schools' => null,
            'reports' => null,
        ];
        $cards = [
            'centres' => 0,
            'qualified' => 0,
            'disqualified' => 0,
            'not_admitted' => 0,
        ];
        $formsTable = [];
        $sexByForm = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $stats['users'] = \Illuminate\Support\Facades\DB::table('users')->count();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('schools')) {
                $stats['schools'] = \Illuminate\Support\Facades\DB::table('schools')->count();
                $cards['centres'] = (int)$stats['schools'];
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('school_result_reports')) {
                $stats['reports'] = \Illuminate\Support\Facades\DB::table('school_result_reports')->count();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                $cards['qualified'] = (int)\Illuminate\Support\Facades\DB::table('students')->where('admitted',1)->count();
                $cards['not_admitted'] = (int)\Illuminate\Support\Facades\DB::table('students')->where('admitted',0)->count();
                // Disqualified placeholder: if there is a status column, use it; else 0
                if (\Illuminate\Support\Facades\Schema::hasColumn('students','status')) {
                    $cards['disqualified'] = (int)\Illuminate\Support\Facades\DB::table('students')->where('status','disqualified')->count();
                }

                // Candidate registration table per Form (I..IV map 1..4)
                $rows = [];
                for ($f=1; $f<=4; $f++) {
                    $total = (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->count();
                    $admitted = (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->where('admitted',1)->count();
                    $notAd = max(0, $total - $admitted);
                    $noExam = (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->whereNull('exam_number')->count();
                    $noPhoto = (\Illuminate\Support\Facades\Schema::hasColumn('students','photo_path'))
                        ? (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->whereNull('photo_path')->count()
                        : 0;
                    $progress = $total>0 ? round(($admitted/$total)*100,1) : 0.0;
                    $rows[] = [
                        'form' => $f,
                        'students' => $total,
                        'candidates' => $total,
                        'not_admitted' => $notAd,
                        'no_exam' => $noExam,
                        'no_photo' => $noPhoto,
                        'progress' => $progress,
                    ];
                }
                $formsTable = $rows;

                // Registration by sex per form
                $sexRows = [];
                for ($f=1; $f<=4; $f++) {
                    $m = (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->where('sex','M')->count();
                    $fe = (int)\Illuminate\Support\Facades\DB::table('students')->where('form',$f)->where('sex','F')->count();
                    $sexRows[] = ['form'=>$f,'male'=>$m,'female'=>$fe];
                }
                $sexByForm = $sexRows;
            }
        } catch (\Throwable $e) {
            // leave nulls on error
        }
        // District-level aggregation if available
        $byDistrict = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('districts') && \Illuminate\Support\Facades\Schema::hasTable('wards') && \Illuminate\Support\Facades\Schema::hasTable('schools')) {
                $byDistrict = \Illuminate\Support\Facades\DB::table('districts as d')
                    ->leftJoin('wards as w','w.district_id','=','d.id')
                    ->leftJoin('schools as s','s.ward_id','=','w.id')
                    ->selectRaw('d.name as district, COUNT(DISTINCT s.id) as schools')
                    ->groupBy('d.name')
                    ->orderBy('d.name')
                    ->get();
            }
        } catch (\Throwable $e) {}
        return view('admin.dashboard', compact('stats','byDistrict','cards','formsTable','sexByForm'));
    }

    public function usersIndex()
    {
        $users = collect();
        $rolesSummary = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $roleFilter = request('role');
                $q = \Illuminate\Support\Facades\DB::table('users')->select('id','name','username','email','role','created_at');
                if ($roleFilter) { $q->where('role',$roleFilter); }
                $users = $q->orderBy('created_at','desc')->limit(200)->get();
                $rolesSummary = \Illuminate\Support\Facades\DB::table('users')
                    ->selectRaw("COALESCE(NULLIF(role,''),'(none)') as role, COUNT(*) as total")
                    ->groupBy('role')->orderBy('total','desc')->get();
            }
        } catch (\Throwable $e) {}
        return view('admin.users.index', compact('users','rolesSummary'));
    }

    public function institutionsIndex()
    {
        $schools = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('schools')) {
                $schools = \Illuminate\Support\Facades\DB::table('schools as s')
                    ->leftJoin('wards as w','w.id','=','s.ward_id')
                    ->leftJoin('districts as d','d.id','=','w.district_id')
                    ->leftJoin('regions as r','r.id','=','d.region_id')
                    ->selectRaw('s.id, s.code, s.name, COALESCE(d.name, "-") as district, COALESCE(r.name, "-") as region')
                    ->orderBy('r.name')->orderBy('d.name')->orderBy('s.name')
                    ->limit(300)
                    ->get();
            }
        } catch (\Throwable $e) {}
        return view('admin.institutions.index', compact('schools'));
    }

    public function analyticsIndex()
    {
        $charts = [
            'usersByRole' => [],
            'schoolsByDistrict' => [],
            'studentsByDistrict' => [],
        ];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $charts['usersByRole'] = \Illuminate\Support\Facades\DB::table('users')
                    ->selectRaw("COALESCE(NULLIF(role,''),'(none)') as label, COUNT(*) as value")
                    ->groupBy('role')->get();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('districts') && \Illuminate\Support\Facades\Schema::hasTable('wards') && \Illuminate\Support\Facades\Schema::hasTable('schools')) {
                $charts['schoolsByDistrict'] = \Illuminate\Support\Facades\DB::table('districts as d')
                    ->leftJoin('wards as w','w.district_id','=','d.id')
                    ->leftJoin('schools as s','s.ward_id','=','w.id')
                    ->selectRaw('d.name as label, COUNT(DISTINCT s.id) as value')
                    ->groupBy('d.name')->orderBy('d.name')->get();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('students') && \Illuminate\Support\Facades\Schema::hasTable('schools')) {
                $charts['studentsByDistrict'] = \Illuminate\Support\Facades\DB::table('districts as d')
                    ->leftJoin('wards as w','w.district_id','=','d.id')
                    ->leftJoin('schools as s','s.ward_id','=','w.id')
                    ->leftJoin('students as st','st.school_id','=','s.id')
                    ->selectRaw('d.name as label, COUNT(DISTINCT st.id) as value')
                    ->groupBy('d.name')->orderBy('d.name')->get();
            }
        } catch (\Throwable $e) {}
        return view('admin.analytics.index', compact('charts'));
    }
}
