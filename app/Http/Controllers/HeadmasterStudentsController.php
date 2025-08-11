<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeadmasterStudentsController extends Controller
{
    private function getHeadmasterPrimarySchool()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'schools')) return [null, null];
        $school = $user->schools()->first();
        if (!$school) return [null, null];
        $schoolId = $school->id ?? null;
        // Try common code columns
        $code = null;
        try {
            if (isset($school->code)) $code = $school->code;
            elseif (isset($school->school_code)) $code = $school->school_code;
            elseif (isset($school->emis_code)) $code = $school->emis_code;
            elseif (\Schema::hasTable('schools')) {
                $q = DB::table('schools')->where('id', $schoolId);
                foreach (['code','school_code','emis_code'] as $col) {
                    if (\Schema::hasColumn('schools', $col)) { $code = $q->value($col); if ($code) break; }
                }
            }
        } catch (\Throwable $e) { /* ignore */ }
        return [$schoolId, $code];
    }

    private function normalizeCode(?string $code): ?string
    {
        if (!$code) return null;
        $c = trim($code);
        if (strtolower(substr($c, 0, 2)) === 's.') {
            $c = substr($c, 2);
        }
        return strtoupper($c);
    }

    private function nextAdmissionNumber(?int $schoolId, ?string $schoolCode): string
    {
        $norm = $this->normalizeCode($schoolCode) ?: 'SCHOOL';
        $prefix = 'S.' . $norm . '.';
        $next = 1;
        try {
            $q = DB::table('students');
            if (\Schema::hasColumn('students', 'school_id') && $schoolId) {
                $q->where('school_id', $schoolId);
            }
            if (\Schema::hasColumn('students', 'admission_number')) {
                $q->where('admission_number', 'like', $prefix.'%');
                $max = null;
                // Fetch latest admission that matches prefix
                $row = $q->orderByDesc('admission_number')->value('admission_number');
                if ($row && str_starts_with($row, $prefix)) {
                    $suffix = substr($row, strlen($prefix));
                    if (ctype_digit($suffix)) {
                        $next = (int)$suffix + 1;
                    }
                }
            }
        } catch (\Throwable $e) {
            $next = 1;
        }
        return $prefix . str_pad((string)($next), 4, '0', STR_PAD_LEFT);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $students = collect();
        $total = 0;

        if (Schema::hasTable('students')) {
            // If a students table exists, attempt a lightweight listing
            $query = DB::table('students');

            // Optional filter by assigned schools if there is a school_id column
            if (Schema::hasColumn('students', 'school_id')) {
                $assignedSchoolIds = method_exists($user, 'schools') ? $user->schools()->pluck('schools.id')->toArray() : [];
                if (!empty($assignedSchoolIds)) {
                    $query->whereIn('school_id', $assignedSchoolIds);
                }
            }

            // Basic select of common columns if present
            $select = [];
            foreach (['id','admission_number','name','gender','class','stream','created_at'] as $col) {
                if (Schema::hasColumn('students', $col)) { $select[] = $col; }
            }
            if (!empty($select)) {
                $query->select($select);
            }

            $students = $query->orderByDesc('id')->limit(50)->get();
            $total = (int) DB::table('students')->count();
        }

        return view('headmaster.students.index', [
            'students' => $students,
            'total' => $total,
        ]);
    }

    public function register(Request $request)
    {
        [$schoolId, $schoolCode] = $this->getHeadmasterPrimarySchool();
        $nextAdmission = $this->nextAdmissionNumber($schoolId, $schoolCode);

        $subjects = [
            'Mathematics','English','Kiswahili','Biology','Chemistry','Physics','Geography','History','Civics','Commerce','Book-keeping','Computer Studies'
        ];

        return view('headmaster.students.register', [
            'nextAdmission' => $nextAdmission,
            'subjects' => $subjects,
            'schoolCode' => $this->normalizeCode($schoolCode),
        ]);
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'admission_number' => 'nullable|string|max:50',
            'gender' => 'required|in:M,F',
            'class' => 'required|in:Form I,Form II,Form III,Form IV',
            'subjects' => 'array',
            'subjects.*' => 'string'
        ]);

        if (!Schema::hasTable('students')) {
            return back()->with('error', 'Students table not found.');
        }

        [$schoolId, $schoolCode] = $this->getHeadmasterPrimarySchool();
        if (!$schoolCode) {
            return back()->with('error', 'Your school has no registration code set. Please contact admin to set the school code before bulk upload.');
        }

        [$schoolId, $schoolCode] = $this->getHeadmasterPrimarySchool();
        if (!$schoolCode) {
            return back()->with('error', 'Your school has no registration code set. Please contact admin to set the school code before registering students.');
        }
        $admission = trim((string)$request->input('admission_number', ''));
        if ($admission === '') {
            $admission = $this->nextAdmissionNumber($schoolId, $schoolCode);
        }

        $payload = [
            'name' => $request->input('full_name'),
            'admission_number' => $admission,
            'gender' => $request->input('gender'),
            'class' => $request->input('class'),
        ];
        if (Schema::hasColumn('students', 'subjects')) {
            $payload['subjects'] = json_encode(array_values($request->input('subjects', [])));
        }
        if (Schema::hasColumn('students', 'school_id')) {
            if ($schoolId) { $payload['school_id'] = $schoolId; }
        }

        if (Schema::hasColumn('students', 'created_at')) { $payload['created_at'] = now(); }
        if (Schema::hasColumn('students', 'updated_at')) { $payload['updated_at'] = now(); }

        DB::table('students')->insert($payload);

        return redirect()->route('headmaster.students.index')->with('success', 'Student registered successfully.');
    }

    public function downloadTemplate(Request $request, string $form)
    {
        $form = strtolower(trim($form));
        // Define subject rules
        $core = ['Mathematics','English','Kiswahili','Biology','Chemistry','Physics','Civics'];
        $others = ['Geography','History','Commerce','Book-keeping','Computer Studies'];

        $headers = ['Full Name','Admission Number','Gender(M/F)','Class','Subjects (comma-separated)'];
        $rows = [];

        if ($form === 'form-ii' || $form === 'form2' || $form === 'form ii') {
            // Form II: all subjects (core + others) allowed, may include options
            $exampleSubjects = array_merge($core, $others);
            $rows[] = ['Asha Juma','ADM-0001','F','Form II', implode(', ', $exampleSubjects)];
        } elseif ($form === 'form-iv' || $form === 'form4' || $form === 'form iv') {
            // Form IV: 7 mandatory core + chosen options
            $exampleSubjects = array_merge($core, ['Geography','History']);
            $rows[] = ['Juma Ally','ADM-0100','M','Form IV', implode(', ', $exampleSubjects)];
        } else {
            $rows[] = ['Student Name','ADM-0001','M','Form I', implode(', ', $core)];
        }

        $filename = 'students_template_'.str_replace(' ','_', $form ?: 'form').'.csv';
        $callback = function() use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $r) { fputcsv($out, $r); }
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadTemplateExcel(Request $request, string $form)
    {
        // If Laravel Excel is not installed, guide the user
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return back()->with('error', 'Excel export not available. Please install Laravel Excel: composer require maatwebsite/excel');
        }

        $form = strtolower(trim($form));
        $core = ['Mathematics','English','Kiswahili','Biology','Chemistry','Physics','Civics'];
        $others = ['Geography','History','Commerce','Book-keeping','Computer Studies'];
        $headings = ['Full Name','Admission Number','Gender(M/F)','Class','Subjects (comma-separated)'];
        if ($form === 'form-ii' || $form === 'form2' || $form === 'form ii') {
            $rows = [ ['Asha Juma','S.SCHOOL.0001','F','Form II', implode(', ', array_merge($core, $others))] ];
            $name = 'students_template_form_ii.xlsx';
        } else {
            $rows = [ ['Juma Ally','S.SCHOOL.0001','M','Form IV', implode(', ', array_merge($core, ['Geography','History']))] ];
            $name = 'students_template_form_iv.xlsx';
        }

        $export = new class($rows, $headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private array $rows; private array $headings;
            public function __construct(array $rows, array $headings) { $this->rows = $rows; $this->headings = $headings; }
            public function array(): array { return $this->rows; }
            public function headings(): array { return $this->headings; }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, $name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:4096',
        ]);

        if (!Schema::hasTable('students')) {
            return back()->with('error', 'Students table not found.');
        }

        $path = $request->file('file')->getRealPath();
        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        $rows = [];
        $inserted = 0; $failed = 0;

        if ($ext === 'xlsx') {
            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return back()->with('error', 'Excel import not available. Please install Laravel Excel: composer require maatwebsite/excel');
            }
            try {
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(null, $request->file('file'));
                $rows = $sheets[0] ?? [];
            } catch (\Throwable $e) {
                return back()->with('error', 'Failed to read the Excel file.');
            }
        } else {
            $handle = fopen($path, 'r');
            if (!$handle) return back()->with('error', 'Could not read uploaded file.');
            $rows[] = fgetcsv($handle); // header
            while (($data = fgetcsv($handle)) !== false) { $rows[] = $data; }
            fclose($handle);
        }

        // Subject rules
        $core = ['Mathematics','English','Kiswahili','Biology','Chemistry','Physics','Civics'];

        // Normalize header + iterate rows
        if (empty($rows)) return back()->with('error', 'No data rows detected.');
        $header = array_map(fn($h)=>strtolower(trim((string)$h)), $rows[0] ?? []);
        $findIdx = function(string $label) use ($header): ?int {
            foreach ($header as $i=>$h) {
                if (str_contains($h, strtolower($label))) return $i;
            }
            return null;
        };
        $idxName = $findIdx('full name');
        $idxAdm = $findIdx('admission');
        $idxGender = $findIdx('gender');
        $idxClass = $findIdx('class');
        $idxSubjects = $findIdx('subjects');
        if ($idxName === null || $idxGender === null || $idxClass === null) {
            return back()->with('error', 'Template headers are invalid. Please use the provided template.');
        }

        for ($r = 1; $r < count($rows); $r++) {
            $data = $rows[$r];
            // Expecting: Full Name, Admission Number, Gender, Class, Subjects
            $fullName = trim((string)($data[$idxName] ?? ''));
            $adm = trim((string)($idxAdm !== null ? ($data[$idxAdm] ?? '') : ''));
            $gender = strtoupper(trim((string)($data[$idxGender] ?? '')));
            $class = trim((string)($data[$idxClass] ?? ''));
            $subjectsCsv = trim((string)($idxSubjects !== null ? ($data[$idxSubjects] ?? '') : ''));
            if ($fullName === '' || ($gender !== 'M' && $gender !== 'F') || $class === '') { $failed++; continue; }
            if ($adm === '') {
                $adm = $this->nextAdmissionNumber($schoolId, $schoolCode);
            }

            $subjectsArr = [];
            if ($subjectsCsv !== '') {
                $subjectsArr = array_values(array_filter(array_map('trim', explode(',', $subjectsCsv))));
            }
            // Apply rules by class
            if (strcasecmp($class, 'Form II') === 0) {
                // Ensure core exist at least; add missing cores
                foreach ($core as $c) if (!in_array($c, $subjectsArr, true)) $subjectsArr[] = $c;
            } elseif (strcasecmp($class, 'Form IV') === 0) {
                // Must include 7 mandatory core subjects
                foreach ($core as $c) if (!in_array($c, $subjectsArr, true)) $subjectsArr[] = $c;
                // Keep any provided options additionally
            }

            $row = [
                'name' => $fullName,
                'admission_number' => $adm,
                'gender' => $gender,
                'class' => $class,
            ];
            if (Schema::hasColumn('students', 'subjects')) {
                $row['subjects'] = json_encode($subjectsArr);
            }
            if (Schema::hasColumn('students', 'school_id')) {
                $user = Auth::user();
                $schoolId = method_exists($user, 'schools') ? $user->schools()->pluck('schools.id')->first() : null;
                if ($schoolId) { $row['school_id'] = $schoolId; }
            }
            if (Schema::hasColumn('students', 'created_at')) { $row['created_at'] = now(); }
            if (Schema::hasColumn('students', 'updated_at')) { $row['updated_at'] = now(); }

            try {
                DB::table('students')->insert($row);
                $inserted++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }
        return back()->with('success', "Bulk upload complete. Inserted: {$inserted}, Failed: {$failed}.");
    }

    public function updateSubjects(Request $request, $id)
    {
        if (!Schema::hasTable('students')) {
            return response()->json(['ok' => false, 'message' => 'Students table not found'], 404);
        }
        $subjects = $request->input('subjects');
        if (is_string($subjects)) {
            $subjects = array_filter(array_map('trim', explode(',', $subjects)));
        }
        if (!is_array($subjects)) {
            return response()->json(['ok' => false, 'message' => 'Invalid subjects format'], 422);
        }
        // Allowed catalog for validation (align with assignSubjects view)
        $catalog = ['031','011','012','013','021','022','023','032','033','041','015','036','061','062'];
        // Normalize, unique, and validate
        $subjects = array_values(array_unique(array_map(fn($c)=>strtoupper((string)$c), $subjects)));
        // Enforce exactly seven mandatory subjects
        if (count($subjects) !== 7) {
            return response()->json(['ok' => false, 'message' => 'Mwanafunzi lazima awe na masomo saba (7) ya lazima.'], 422);
        }
        // Ensure all are allowed codes
        foreach ($subjects as $code) {
            if (!in_array($code, $catalog, true)) {
                return response()->json(['ok' => false, 'message' => 'Kodi ya somo haijulikani: '.$code], 422);
            }
        }
        $data = [];
        if (Schema::hasColumn('students', 'subjects')) {
            $data['subjects'] = json_encode($subjects);
        }
        if (Schema::hasColumn('students', 'updated_at')) { $data['updated_at'] = now(); }
        try {
            DB::table('students')->where('id', $id)->update($data);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Failed to save'], 500);
        }
    }

    public function uploadImage(Request $request, $id)
    {
        if (!Schema::hasTable('students')) { return back()->with('error','Students table not found'); }
        $request->validate(['photo'=>'nullable|image|max:2048']);
        if (!$request->hasFile('photo')) { return back()->with('error','No image selected'); }
        $path = $request->file('photo')->store('students','public');
        $data = [];
        if (Schema::hasColumn('students', 'photo_path')) { $data['photo_path'] = $path; }
        if (Schema::hasColumn('students', 'updated_at')) { $data['updated_at'] = now(); }
        DB::table('students')->where('id',$id)->update($data);
        return back()->with('success','Photo uploaded');
    }

    public function showProfile($id)
    {
        if (!Schema::hasTable('students')) { return back()->with('error','Students table not found'); }
        $q = DB::table('students')->where('id', $id);
        $select = [];
        // Always include id
        if (Schema::hasColumn('students','id')) { $select[] = 'id'; }
        foreach (['name','full_name','student_name','admission_number','class','gender','subjects','photo_path'] as $c) {
            if (Schema::hasColumn('students',$c)) $select[] = $c;
        }
        if (!empty($select)) $q->select(array_unique($select));
        $student = $q->first();
        if (!$student) { return back()->with('error','Student not found'); }
        // Prepare subjects list for view
        $subjects_list = [];
        if (isset($student->subjects)) {
            if (is_string($student->subjects)) {
                $dec = json_decode($student->subjects, true);
                $subjects_list = is_array($dec) ? $dec : [];
            } elseif (is_array($student->subjects)) {
                $subjects_list = $student->subjects;
            }
        }
        $student->subjects_list = $subjects_list;
        return view('headmaster.students.show', compact('student'));
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('students')) { return back()->with('error','Students table not found'); }
        try { DB::table('students')->where('id',$id)->delete(); }
        catch (\Throwable $e) { return back()->with('error','Failed to delete student'); }
        return back()->with('success','Student deleted');
    }

    public function assignSubjects(Request $request)
    {
        $user = Auth::user();
        $students = collect();
        if (Schema::hasTable('students')) {
            $q = DB::table('students');
            if (Schema::hasColumn('students','school_id')) {
                $schoolIds = method_exists($user,'schools') ? $user->schools()->pluck('schools.id')->toArray() : [];
                if (!empty($schoolIds)) $q->whereIn('school_id', $schoolIds);
            }
            $select = [];
            // Always include id
            if (Schema::hasColumn('students','id')) { $select[] = 'id'; }
            foreach (['name','full_name','student_name','admission_number','class','gender','subjects'] as $c) {
                if (Schema::hasColumn('students',$c)) $select[] = $c;
            }
            if (!empty($select)) { $q->select(array_unique($select)); }

            // Determine order by column
            $orderBy = null;
            foreach (['name','full_name','student_name','admission_number','id'] as $c) {
                if (Schema::hasColumn('students',$c)) { $orderBy = $c; break; }
            }
            if ($orderBy) { $q->orderBy($orderBy); }

            $students = $q->limit(100)->get();
        }

        // Basic subject catalog (codes as shown in screenshot-like UI)
        $catalog = [
            '031','011','012','013','021','022','023','032','033','041','015','036','061','062'
        ];

        return view('headmaster.students.assign', [
            'students' => $students,
            'catalog' => $catalog,
        ]);
    }
}
