<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeadmasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || ($user->role ?? null) !== 'headmaster') {
                abort(403, 'Access denied');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Enforce profile completion before accessing dashboard
        if (empty($user->phone) || empty($user->date_of_birth) || empty($user->bank_number)) {
            return redirect()->route('headmaster.profile')->with([
                'status' => 'Karibu! Tafadhali kamilisha wasifu wako kabla ya kuendelea.',
                'welcome_headmaster' => true,
            ]);
        }

        $query = DB::table('headmaster_students')->where('user_id', $userId);

        $schools = [];
        if (Schema::hasColumn('headmaster_students','school_name')) {
            $schools = DB::table('headmaster_students')
                ->where('user_id', $userId)
                ->whereNotNull('school_name')
                ->distinct()
                ->orderBy('school_name')
                ->pluck('school_name')
                ->toArray();
        } elseif (!empty($user->institution)) {
            $schools = [$user->institution];
        }

        // Optional filter by school
        $activeSchool = request('school');
        if ($activeSchool && Schema::hasColumn('headmaster_students','school_name')) {
            $query->where('school_name', $activeSchool);
        }

        // Totals
        $totalStudents = (clone $query)->count();
        $totalSchools = count($schools);
        $totalTeachers = 0;
        if (Schema::hasTable('headmaster_teachers')) {
            $tq = DB::table('headmaster_teachers')->where('user_id', $userId);
            if ($activeSchool && Schema::hasColumn('headmaster_teachers','school_name')) {
                $tq->where('school_name', $activeSchool);
            }
            $totalTeachers = $tq->count();
        }

        // Forms distribution for bar chart
        $forms = (clone $query)
            ->select('form_level', DB::raw('COUNT(*) as total'))
            ->groupBy('form_level')
            ->orderBy('form_level')
            ->get();

        // Gender distribution for pie chart (if gender exists)
        $gender = [];
        if (Schema::hasColumn('headmaster_students','gender')) {
            $gender = (clone $query)
                ->select('gender', DB::raw('COUNT(*) as total'))
                ->groupBy('gender')
                ->get();
        }

        // Recent activity: recent student records as a proxy
        $recent = (clone $query)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('headmaster.dashboard', compact(
            'forms','recent','totalStudents','totalSchools','totalTeachers','schools','activeSchool','gender'
        ));
    }

    public function uploadForm()
    {
        return view('headmaster.upload');
    }

    public function uploadStore(Request $request)
    {
        $request->validate([
            'csv' => ['required','file','mimes:csv,txt','max:2048'],
            'form_level' => ['nullable','string','max:50'],
        ]);

        $userId = Auth::id();
        $formLevelDefault = $request->input('form_level');

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        $header = null; $rows = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if ($header === null) { $header = $data; continue; }
            $row = array_combine($header, $data);
            if (!$row) { continue; }
            $fullName = $row['full_name'] ?? $row['name'] ?? null;
            if (!$fullName) { continue; }
            DB::table('headmaster_students')->insert([
                'user_id' => $userId,
                'full_name' => $fullName,
                'admission_no' => $row['admission_no'] ?? null,
                'form_level' => $row['form_level'] ?? $formLevelDefault,
                'stream' => $row['stream'] ?? null,
                'gender' => $row['gender'] ?? null,
                'dob' => isset($row['dob']) && $row['dob'] ? date('Y-m-d', strtotime($row['dob'])) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rows++;
        }
        fclose($handle);

        return redirect()->route('headmaster.dashboard')->with('status', "Uploaded {$rows} students.");
    }
}
