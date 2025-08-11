<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeadmasterPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ((Auth::user()->role ?? null) !== 'headmaster') {
                abort(403);
            }
            return $next($request);
        });
    }

    // Students
    public function studentsRegister() { return view('headmaster.students.register'); }
    public function studentsManage() { return view('headmaster.students.manage'); }
    public function studentsIndex() { return view('headmaster.students.index'); }

    // Teachers
    public function teachersProposals() { return view('headmaster.teachers.proposals'); }
    public function teachersSelected() { return view('headmaster.teachers.selected'); }

    // Reports
    public function reportsIndex()
    {
        $user = Auth::user();
        $schoolCodes = [];
        $schoolNames = [];

        // Try relationship
        if (method_exists($user, 'schools')) {
            try {
                $schools = $user->schools()->get();
                foreach ($schools as $s) {
                    $schoolNames[] = $s->name ?? ($s->school_name ?? 'School');
                    foreach (['code','school_code','emis_code','reg_no','registration_no'] as $col) {
                        if (!empty($s->{$col})) { $schoolCodes[] = ltrim((string)$s->{$col}, 'S.'); break; }
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Fallback via headmaster_students
        if (empty($schoolCodes) && Schema::hasTable('headmaster_students')) {
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $rows = DB::table('headmaster_students')->where('user_id',$user->id)->when($hasName, fn($q)=>$q->whereNotNull('school_name'))->distinct()->get();
            foreach ($rows as $r) {
                $schoolNames[] = $hasName ? ($r->school_name ?? 'School') : 'School';
                if ($hasCode && !empty($r->school_code)) $schoolCodes[] = ltrim((string)$r->school_code, 'S.');
            }
        }

        // Normalize and uniquify
        $schoolCodes = array_values(array_unique(array_filter($schoolCodes)));
        $schoolNames = array_values(array_unique(array_filter($schoolNames)));

        // Students stats (try 'students' first)
        $studentsTotal = 0; $gender = ['male'=>0,'female'=>0]; $byForm = [];
        if (Schema::hasTable('students')) {
            $sq = DB::table('students');
            // Scope by school code if present
            foreach (['school_code','code','reg_no','registration_no'] as $col) {
                if (!empty($schoolCodes) && Schema::hasColumn('students',$col)) { $sq->whereIn($col, $schoolCodes); break; }
            }
            $students = $sq->get();
            $studentsTotal = $students->count();
            if (Schema::hasColumn('students','gender')) {
                $gender['male'] = $students->where('gender','male')->count() + $students->where('gender','M')->count();
                $gender['female'] = $students->where('gender','female')->count() + $students->where('gender','F')->count();
            }
            // determine class/form column
            $formCol = null; foreach(['form','class','level'] as $fc) if (Schema::hasColumn('students',$fc)) { $formCol = $fc; break; }
            if ($formCol) {
                $byForm = $students->groupBy($formCol)->map->count()->toArray();
            }
        } elseif (Schema::hasTable('headmaster_students')) {
            $sq = DB::table('headmaster_students')->where('user_id',$user->id);
            $students = $sq->get();
            $studentsTotal = $students->count();
            if (Schema::hasColumn('headmaster_students','gender')) {
                $gender['male'] = $students->where('gender','male')->count() + $students->where('gender','M')->count();
                $gender['female'] = $students->where('gender','female')->count() + $students->where('gender','F')->count();
            }
            $formCol = null; foreach(['form','class','level'] as $fc) if (Schema::hasColumn('headmaster_students',$fc)) { $formCol = $fc; break; }
            if ($formCol) { $byForm = $students->groupBy($formCol)->map->count()->toArray(); }
        }

        // Teachers count (users with role like teacher)
        $teachersTotal = 0;
        if (Schema::hasTable('users')) {
            $uq = DB::table('users');
            if (Schema::hasColumn('users','role')) $uq->where('role','like','%teacher%');
            // Scope by school via subjects->teacher_id fallback not trivial; leave global or enhance when schema available
            $teachersTotal = $uq->count();
        }

        // Subjects count
        $subjectsTotal = Schema::hasTable('subjects') ? DB::table('subjects')->count() : 0;

        $stats = [
            'schools' => $schoolNames,
            'students_total' => $studentsTotal,
            'teachers_total' => $teachersTotal,
            'subjects_total' => $subjectsTotal,
            'gender' => $gender,
            'by_form' => $byForm,
        ];

        return view('headmaster.reports.index', compact('stats'));
    }
    public function reportsResults()
    {
        $user = Auth::user();
        $schoolCodes = [];
        $schoolNames = [];

        // Resolve school scope (same approach as reportsIndex)
        if (method_exists($user, 'schools')) {
            try {
                $schools = $user->schools()->get();
                foreach ($schools as $s) {
                    $schoolNames[] = $s->name ?? ($s->school_name ?? 'School');
                    foreach (['code','school_code','emis_code','reg_no','registration_no'] as $col) {
                        if (!empty($s->{$col})) { $schoolCodes[] = ltrim((string)$s->{$col}, 'S.'); break; }
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        if (empty($schoolCodes) && Schema::hasTable('headmaster_students')) {
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $rows = DB::table('headmaster_students')->where('user_id',$user->id)->when($hasName, fn($q)=>$q->whereNotNull('school_name'))->distinct()->get();
            foreach ($rows as $r) {
                $schoolNames[] = $hasName ? ($r->school_name ?? 'School') : 'School';
                if ($hasCode && !empty($r->school_code)) $schoolCodes[] = ltrim((string)$r->school_code, 'S.');
            }
        }
        $schoolCodes = array_values(array_unique(array_filter($schoolCodes)));

        $reports = collect();
        // Primary table for uploaded result reports by admin
        if (Schema::hasTable('school_result_reports')) {
            $q = DB::table('school_result_reports');
            $select = [];
            foreach (['id','title','exam','term','year','school_code','pdf_path','status','created_at'] as $c) if (Schema::hasColumn('school_result_reports',$c)) $select[]=$c;
            if (!empty($select)) $q->select($select);
            if (!empty($schoolCodes) && Schema::hasColumn('school_result_reports','school_code')) {
                $q->whereIn('school_code', $schoolCodes);
            }
            $q->orderBy(Schema::hasColumn('school_result_reports','created_at') ? 'created_at' : 'id', 'desc');
            $reports = $q->get();
        }

        // Resolve download URLs (public disk assumed)
        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $reports = $reports->map(function($r) use ($disk){
                $path = $r->pdf_path ?? null;
                $r->download_url = ($path && $disk->exists($path)) ? $disk->url($path) : null;
                return $r;
            });
        } catch (\Throwable $e) { /* ignore */ }

        return view('headmaster.reports.results', [
            'reports' => $reports,
            'schools' => $schoolNames,
        ]);
    }

    // Institution
    public function institutionProfile(Request $request)
    {
        $user = Auth::user();
        // Resolve assigned schools (name + code)
        $schools = [];
        if (method_exists($user, 'schools')) {
            try {
                foreach ($user->schools()->get() as $s) {
                    $name = $s->name ?? ($s->school_name ?? 'School');
                    $code = null; foreach(['code','school_code','emis_code','reg_no','registration_no'] as $c){ if(!empty($s->{$c})) { $code = (string)$s->{$c}; break; } }
                    $schools[] = ['name'=>$name,'code'=>$code];
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        if (empty($schools) && Schema::hasTable('headmaster_students')) {
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $rows = DB::table('headmaster_students')->where('user_id',$user->id)->distinct()->get();
            foreach ($rows as $r) {
                $schools[] = ['name'=>$hasName?($r->school_name??'School'):'School', 'code'=>$hasCode?($r->school_code??null):null];
            }
        }
        // Select school (if multi)
        $selectedCode = $request->query('school');
        if (!$selectedCode && !empty($schools)) { $selectedCode = $schools[0]['code']; }

        // Load school details from possible tables
        $details = [];
        if ($selectedCode) {
            if (Schema::hasTable('schools')) {
                $row = DB::table('schools')->when(Schema::hasColumn('schools','code'), fn($q)=>$q->where('code',$selectedCode))
                    ->when(!Schema::hasColumn('schools','code') && Schema::hasColumn('schools','school_code'), fn($q)=>$q->where('school_code',$selectedCode))
                    ->first();
                if ($row) $details = (array)$row;
            } elseif (Schema::hasTable('centres')) {
                $row = DB::table('centres')
                    ->when(Schema::hasColumn('centres','reg_no'), fn($q)=>$q->where('reg_no', 'S.'.$selectedCode))
                    ->when(!Schema::hasColumn('centres','reg_no') && Schema::hasColumn('centres','code'), fn($q)=>$q->where('code', $selectedCode))
                    ->first();
                if ($row) $details = (array)$row;
            }
        }

        // Aggregate metrics
        $studentsCount = 0; $teachersCount = 0; $subjectsCount = 0; $classesCount = 0;
        if ($selectedCode) {
            // Students
            if (Schema::hasTable('students')) {
                $q = DB::table('students');
                foreach(['school_code','code','reg_no'] as $c){ if(Schema::hasColumn('students',$c)) { $q->where($c, $c==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
                try { $studentsCount = (int)$q->count(); } catch (\Throwable $e) {}
            } elseif (Schema::hasTable('headmaster_students')) {
                try { $studentsCount = (int) DB::table('headmaster_students')->where('user_id',$user->id)->when(Schema::hasColumn('headmaster_students','school_code'), fn($q)=>$q->where('school_code',$selectedCode))->count(); } catch (\Throwable $e) {}
            }
            // Teachers
            if (Schema::hasTable('teachers')) {
                $q = DB::table('teachers');
                foreach(['school_code','code','reg_no'] as $c){ if(Schema::hasColumn('teachers',$c)) { $q->where($c, $c==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
                try { $teachersCount = (int)$q->count(); } catch (\Throwable $e) {}
            }
            // Subjects
            if (Schema::hasTable('subjects')) {
                $q = DB::table('subjects');
                if (Schema::hasColumn('subjects','school_code')) { $q->where('school_code',$selectedCode); }
                try { $subjectsCount = (int)$q->count(); } catch (\Throwable $e) {}
            }
            // Classes/streams
            if (Schema::hasTable('classes')) {
                $q = DB::table('classes');
                foreach(['school_code','code','reg_no'] as $c){ if(Schema::hasColumn('classes',$c)) { $q->where($c, $c==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
                try { $classesCount = (int)$q->count(); } catch (\Throwable $e) {}
            }
        }

        // Headmaster and location hints
        $nameParts = array_filter([
            $user->first_name ?? null,
            $user->middle_name ?? null,
            $user->last_name ?? null,
        ]);
        $headmasterName = trim($nameParts ? implode(' ', $nameParts) : (string)($user->name ?? ''));
        $region = $details['region'] ?? ($details['school_region'] ?? ($details['province'] ?? null));
        $district = $details['district'] ?? ($details['council'] ?? ($details['lga'] ?? null));

        return view('headmaster.institution.profile', [
            'schools' => $schools,
            'selectedCode' => $selectedCode,
            'details' => $details,
            'studentsCount' => $studentsCount,
            'teachersCount' => $teachersCount,
            'subjectsCount' => $subjectsCount,
            'classesCount' => $classesCount,
            'headmasterName' => $headmasterName,
            'region' => $region,
            'district' => $district,
        ]);
    }

    public function institutionManage(Request $request)
    {
        $user = Auth::user();
        // Permission: allow if role contains 'admin' or boolean column 'can_manage_schools' is truthy
        $canManage = false;
        try {
            $role = strtolower((string)($user->role ?? ''));
            $canManage = str_contains($role,'admin') || (property_exists($user,'can_manage_schools') && (bool)$user->can_manage_schools);
        } catch (\Throwable $e) { $canManage = false; }

        // Schools list (reuse logic)
        $schools = [];
        if (method_exists($user, 'schools')) {
            try { foreach ($user->schools()->get() as $s) { $name = $s->name ?? ($s->school_name ?? 'School'); $code=null; foreach(['code','school_code','emis_code','reg_no','registration_no'] as $c){ if(!empty($s->{$c})) { $code = (string)$s->{$c}; break; } } $schools[]=['name'=>$name,'code'=>$code]; } } catch(\Throwable $e){}
        }
        if (empty($schools) && Schema::hasTable('headmaster_students')) {
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $rows = DB::table('headmaster_students')->where('user_id',$user->id)->distinct()->get();
            foreach ($rows as $r) { $schools[]=['name'=>$hasName?($r->school_name??'School'):'School','code'=>$hasCode?($r->school_code??null):null]; }
        }
        $selectedCode = $request->query('school'); if(!$selectedCode && !empty($schools)) $selectedCode=$schools[0]['code'];

        // Load details for manage view
        $details = [];
        if ($selectedCode) {
            if (Schema::hasTable('schools')) {
                $row = DB::table('schools')->when(Schema::hasColumn('schools','code'), fn($q)=>$q->where('code',$selectedCode))
                    ->when(!Schema::hasColumn('schools','code') && Schema::hasColumn('schools','school_code'), fn($q)=>$q->where('school_code',$selectedCode))
                    ->first();
                if ($row) $details = (array)$row;
            } elseif (Schema::hasTable('centres')) {
                $row = DB::table('centres')->when(Schema::hasColumn('centres','reg_no'), fn($q)=>$q->where('reg_no','S.'.$selectedCode))
                    ->when(!Schema::hasColumn('centres','reg_no') && Schema::hasColumn('centres','code'), fn($q)=>$q->where('code',$selectedCode))
                    ->first();
                if ($row) $details = (array)$row;
            }
        }

        return view('headmaster.institution.manage', compact('schools','selectedCode','details','canManage'));
    }

    public function institutionPerformance(Request $request)
    {
        $user = Auth::user();
        // Schools list
        $schools = [];
        if (method_exists($user, 'schools')) {
            try { foreach ($user->schools()->get() as $s) { $name=$s->name ?? ($s->school_name ?? 'School'); $code=null; foreach(['code','school_code','emis_code','reg_no','registration_no'] as $c){ if(!empty($s->{$c})) { $code=(string)$s->{$c}; break; } } $schools[]=['name'=>$name,'code'=>$code]; } } catch(\Throwable $e){}
        }
        if (empty($schools) && Schema::hasTable('headmaster_students')) {
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $rows = DB::table('headmaster_students')->where('user_id',$user->id)->distinct()->get();
            foreach ($rows as $r) { $schools[]=['name'=>$hasName?($r->school_name??'School'):'School','code'=>$hasCode?($r->school_code??null):null]; }
        }
        $selectedCode = $request->query('school'); if(!$selectedCode && !empty($schools)) $selectedCode=$schools[0]['code'];

        // Compute performance metrics from best-effort tables
        $grades = ['A'=>0,'B'=>0,'C'=>0,'D'=>0,'F'=>0];
        $avgPoints = null; $gpa = null; $total = 0;

        $resultRows = collect();
        // Try student_results
        if ($selectedCode && Schema::hasTable('student_results')) {
            $q = DB::table('student_results');
            foreach(['school_code','code','reg_no'] as $col){ if(Schema::hasColumn('student_results',$col)) { $q->where($col, $col==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
            $resultRows = $q->get();
        } elseif ($selectedCode && Schema::hasTable('results')) {
            $q = DB::table('results');
            foreach(['school_code','code','reg_no'] as $col){ if(Schema::hasColumn('results',$col)) { $q->where($col, $col==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
            $resultRows = $q->get();
        } elseif ($selectedCode && Schema::hasTable('exam_results')) {
            $q = DB::table('exam_results');
            foreach(['school_code','code','reg_no'] as $col){ if(Schema::hasColumn('exam_results',$col)) { $q->where($col, $col==='reg_no' ? 'S.'.$selectedCode : $selectedCode); break; } }
            $resultRows = $q->get();
        }

        if ($resultRows->count()) {
            // Count grades if 'grade' column exists
            if ($resultRows->isNotEmpty()) {
                if (isset($resultRows[0]->grade)) {
                    foreach ($resultRows as $r) {
                        $g = strtoupper((string)($r->grade ?? ''));
                        if (isset($grades[$g])) $grades[$g]++;
                    }
                }
                // Average points if 'points' or 'score' exists
                $pointsCol = null; foreach(['points','gpa_points','score','avg_points'] as $pc){ if(isset($resultRows[0]->{$pc})) { $pointsCol=$pc; break; } }
                if ($pointsCol) {
                    $total = $resultRows->count();
                    $sum = 0; foreach($resultRows as $r){ $sum += (float)($r->{$pointsCol} ?? 0); }
                    $avgPoints = $total ? round($sum / $total, 2) : null;
                }
                // GPA if 'gpa' exists or derive from points
                if (isset($resultRows[0]->gpa)) {
                    $sum=0; $total=$resultRows->count(); foreach($resultRows as $r){ $sum += (float)($r->gpa ?? 0); } $gpa = $total? round($sum/$total,2):null;
                } elseif ($avgPoints !== null) {
                    // Heuristic mapping points->GPA (fallback): normalize to 0-5 range
                    $gpa = max(0, min(5, round($avgPoints / 20, 2)));
                }
            }
        }

        return view('headmaster.institution.performance', [
            'schools'=>$schools,
            'selectedCode'=>$selectedCode,
            'grades'=>$grades,
            'avgPoints'=>$avgPoints,
            'gpa'=>$gpa,
            'total'=>$resultRows->count(),
        ]);
    }


    // Settings
    public function settingsIndex()
    {
        $user = Auth::user();
        $prefs = [
            'locale' => 'en',
            'theme' => 'light',
            'notifications_email' => '1',
            'notifications_sms' => '0',
            'term_year' => date('Y'),
        ];
        // Load from user_settings if exists
        if (Schema::hasTable('user_settings')) {
            try {
                $rows = DB::table('user_settings')->where('user_id', $user->id)->get();
                foreach ($rows as $r) {
                    if (array_key_exists($r->key, $prefs)) $prefs[$r->key] = (string)($r->value ?? '');
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        // Permission: admin/superadmin OR explicit flag in settings
        $role = (string)($user->role ?? '');
        $canEdit = in_array(strtolower($role), ['admin','superadmin']);
        if (!$canEdit && Schema::hasTable('user_settings')) {
            try {
                $flag = DB::table('user_settings')->where('user_id', $user->id)->where('key','headmaster_prefs_edit')->value('value');
                $canEdit = $flag === '1' || $flag === 1;
            } catch (\Throwable $e) { /* ignore */ }
        }
        return view('headmaster.settings.index', compact('prefs','canEdit'));
    }

    public function settingsSave(Request $request)
    {
        $user = Auth::user();
        // Permission gate
        $role = (string)($user->role ?? '');
        $canEdit = in_array(strtolower($role), ['admin','superadmin']);
        if (!$canEdit && Schema::hasTable('user_settings')) {
            $flag = DB::table('user_settings')->where('user_id', $user->id)->where('key','headmaster_prefs_edit')->value('value');
            $canEdit = $flag === '1' || $flag === 1;
        }
        if (!$canEdit) {
            return back()->withErrors(['permission' => 'Editing preferences is locked. Please contact admin.']);
        }

        $data = $request->validate([
            'locale' => ['required','string','max:10'],
            'theme' => ['required','in:light,dark'],
            'notifications_email' => ['nullable','in:0,1'],
            'notifications_sms' => ['nullable','in:0,1'],
            'term_year' => ['required','string','max:10'],
        ]);

        if (!Schema::hasTable('user_settings')) {
            return back()->withErrors(['system' => 'Settings storage is not available. Please run migrations.']);
        }

        $pairs = [
            'locale' => $data['locale'],
            'theme' => $data['theme'],
            'notifications_email' => $request->input('notifications_email','0'),
            'notifications_sms' => $request->input('notifications_sms','0'),
            'term_year' => $data['term_year'],
        ];
        foreach ($pairs as $k=>$v) {
            try {
                $exists = DB::table('user_settings')->where('user_id',$user->id)->where('key',$k)->exists();
                if ($exists) {
                    DB::table('user_settings')->where('user_id',$user->id)->where('key',$k)->update(['value'=>(string)$v, 'updated_at'=>now()]);
                } else {
                    DB::table('user_settings')->insert(['user_id'=>$user->id,'key'=>$k,'value'=>(string)$v,'created_at'=>now(),'updated_at'=>now()]);
                }
            } catch (\Throwable $e) { /* ignore single key errors */ }
        }

        return back()->with('status','Preferences saved.');
    }

    // Profile (Headmaster-only view/update)
    public function profileShow() {
        $presets = [
            'avatars/google/avatar1.svg',
            'avatars/google/avatar2.svg',
        ];
        $user = Auth::user();
        $assignedSchools = [];
        $schoolMeta = [];

        // Try relationship first if available
        if (method_exists($user, 'schools')) {
            try {
                $schools = $user->schools()->get();
                foreach ($schools as $s) {
                    $name = $s->name ?? ($s->school_name ?? 'School');
                    $code = null;
                    foreach (['code','school_code','emis_code','reg_no','registration_no'] as $col) {
                        if (isset($s->{$col}) && $s->{$col}) { $code = $s->{$col}; break; }
                    }
                    $assignedSchools[] = $name;
                    $schoolMeta[] = ['name' => $name, 'code' => $code];
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Fallback: headmaster_students table
        if (empty($schoolMeta) && Schema::hasTable('headmaster_students')) {
            $query = DB::table('headmaster_students')->where('user_id', $user->id);
            $hasName = Schema::hasColumn('headmaster_students','school_name');
            $hasCode = Schema::hasColumn('headmaster_students','school_code');
            $rows = $query->when($hasName, fn($q)=>$q->whereNotNull('school_name'))
                          ->distinct()->get();
            foreach ($rows as $r) {
                $name = $hasName ? ($r->school_name ?? 'School') : 'School';
                $code = $hasCode ? ($r->school_code ?? null) : null;
                $assignedSchools[] = $name;
                $schoolMeta[] = ['name' => $name, 'code' => $code];
            }
        }

        // Fallback to single institution string
        if (empty($schoolMeta) && !empty($user->institution)) {
            $assignedSchools = [$user->institution];
            $schoolMeta[] = ['name' => $user->institution, 'code' => null];
        }

        return view('headmaster.profile', compact('presets','assignedSchools','schoolMeta'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        // Validation for allowed fields only (Tanzania phone format)
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','regex:/^(\\+255|0)(6|7)\\d{8}$/'],
            'date_of_birth' => ['nullable','date'],
            'bank_number' => ['nullable','string','max:100'],
            'avatar_choice' => ['nullable','string','max:255'],
        ], [
            'phone.regex' => 'Phone must be a valid Tanzania number (e.g. +2557XXXXXXXX or 07XXXXXXXX).',
        ]);

        // Handle preset avatar choice only
        $presets = [
            'avatars/google/avatar1.svg',
            'avatars/google/avatar2.svg',
        ];
        $choice = $data['avatar_choice'] ?? null;
        if ($choice === 'none') {
            // Clear previous storage file only if it was uploaded
            if ($user->avatar_path && str_starts_with($user->avatar_path, 'profiles/') && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
        } elseif ($choice && in_array($choice, $presets, true)) {
            // Switch to preset; delete old uploaded file if applicable
            if ($user->avatar_path && str_starts_with($user->avatar_path, 'profiles/') && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $choice;
        }

        // Update allowed fields only (role is intentionally NOT updatable here)
        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->date_of_birth = $data['date_of_birth'] ?? $user->date_of_birth;
        $user->bank_number = $data['bank_number'] ?? $user->bank_number;
        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }

    public function suggestionStore(Request $request)
    {
        $data = $request->validate([
            'message' => ['required','string','max:1000'],
        ]);
        // For now, store suggestion via a generic table if exists, otherwise log.
        if (Schema::hasTable('support_messages')) {
            DB::table('support_messages')->insert([
                'user_id' => Auth::id(),
                'message' => '[HEADMASTER-SUGGESTION] '.$data['message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            \Log::info('Headmaster suggestion', ['user_id'=>Auth::id(),'message'=>$data['message']]);
        }
        return back()->with('status','Suggestion sent. Thank you!');
    }
}
