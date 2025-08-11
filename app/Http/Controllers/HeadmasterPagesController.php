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
    public function reportsIndex() { return view('headmaster.reports.index'); }
    public function reportsResults() { return view('headmaster.reports.results'); }

    // Institution
    public function institutionProfile() { return view('headmaster.institution.profile'); }
    public function institutionManage() { return view('headmaster.institution.manage'); }
    public function institutionPerformance() { return view('headmaster.institution.performance'); }

    // Settings
    public function settingsIndex() { return view('headmaster.settings.index'); }

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
