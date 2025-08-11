<?php

namespace App\Http\Controllers\Marking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MarksController extends Controller
{
    // POST /marking/api/marks
    public function upsert(Request $request)
    {
        $userId = Auth::id();
        $data = $request->validate([
            'school_id' => ['required','integer'],
            'student_id' => ['required','integer'],
            'subject_id' => ['required','integer'],
            'form' => ['nullable','integer','min:1','max:6'],
            'score' => ['nullable','numeric','min:0','max:100'],
        ]);

        // Check school assignment
        $allowedSchool = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->where('school_id', $data['school_id'])
            ->exists();
        if (!$allowedSchool) {
            throw new HttpException(403, 'Not assigned to this school');
        }

        // Check subject assignment for edit rights
        $canEdit = DB::table('user_subject_assignments')
            ->where('user_id', $userId)
            ->where('school_id', $data['school_id'])
            ->where('subject_id', $data['subject_id'])
            ->exists();
        if (!$canEdit) {
            return response()->json(['ok'=>false,'error'=>'Not assigned to this subject'], 403);
        }

        // Ensure student belongs to school
        $studentOk = DB::table('students')
            ->where('id', $data['student_id'])
            ->where('school_id', $data['school_id'])
            ->exists();
        if (!$studentOk) {
            throw new HttpException(422, 'Student not in school');
        }

        // Upsert mark
        $now = now();
        $formVal = $data['form'] ?? DB::table('students')->where('id',$data['student_id'])->value('form');
        $scoreVal = $data['score'];
        $existing = DB::table('marks')
            ->where('student_id',$data['student_id'])
            ->where('school_id',$data['school_id'])
            ->where('subject_id',$data['subject_id'])
            ->first();
        if ($existing) {
            DB::table('marks')
                ->where('id', $existing->id)
                ->update([
                    'form' => $formVal,
                    'score' => $scoreVal,
                    'entered_by' => $userId,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('marks')->insert([
                'student_id' => $data['student_id'],
                'school_id' => $data['school_id'],
                'subject_id' => $data['subject_id'],
                'form' => $formVal,
                'score' => $scoreVal,
                'entered_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return response()->json(['ok'=>true]);
    }

    // GET /marking/api/assignments?school_id=ID
    public function assignments(Request $request)
    {
        $userId = Auth::id();
        $schoolId = (int) $request->query('school_id');
        if (!$schoolId) {
            return response()->json(['ok'=>false,'error'=>'school_id is required'], 422);
        }

        // Ensure user is assigned to the school
        $allowedSchool = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->exists();
        if (!$allowedSchool) {
            return response()->json(['ok'=>false,'error'=>'Not assigned to this school'], 403);
        }

        $subjects = DB::table('user_subject_assignments as usa')
            ->join('subjects as s','s.id','=','usa.subject_id')
            ->where('usa.user_id', $userId)
            ->where('usa.school_id', $schoolId)
            ->orderBy('s.name')
            ->get(['s.id','s.name']);

        return response()->json([
            'ok' => true,
            'school_id' => $schoolId,
            'subjects' => $subjects,
        ]);
    }

    // GET /marking/api/recent
    public function recent(Request $request)
    {
        $userId = Auth::id();
        $assignedSchoolIds = DB::table('user_school_assignments')
            ->where('user_id', $userId)
            ->pluck('school_id');

        // Summary counts
        $today = now()->startOfDay();
        $hourAgo = now()->subHour();
        $totalToday = DB::table('marks')
            ->whereIn('school_id', $assignedSchoolIds)
            ->where('updated_at', '>=', $today)
            ->count();
        $lastHour = DB::table('marks')
            ->whereIn('school_id', $assignedSchoolIds)
            ->where('updated_at', '>=', $hourAgo)
            ->count();
        // Per-user counts (who last updated the row)
        $mineToday = DB::table('marks')
            ->whereIn('school_id', $assignedSchoolIds)
            ->where('entered_by', $userId)
            ->where('updated_at', '>=', $today)
            ->count();
        $mineHour = DB::table('marks')
            ->whereIn('school_id', $assignedSchoolIds)
            ->where('entered_by', $userId)
            ->where('updated_at', '>=', $hourAgo)
            ->count();

        // Recent marks list
        $recent = DB::table('marks as m')
            ->join('students as st','st.id','=','m.student_id')
            ->join('subjects as su','su.id','=','m.subject_id')
            ->join('schools as sc','sc.id','=','m.school_id')
            ->whereIn('m.school_id', $assignedSchoolIds)
            ->orderByDesc('m.updated_at')
            ->limit(10)
            ->get([
                'm.id','m.score','m.updated_at',
                'st.exam_number','st.first_name','st.last_name','st.form','st.sex',
                'su.name as subject',
                'sc.name as school'
            ]);

        return response()->json([
            'ok' => true,
            'summary' => [
                'today' => $totalToday,
                'last_hour' => $lastHour,
                'mine_today' => $mineToday,
                'mine_last_hour' => $mineHour,
            ],
            'recent' => $recent,
        ]);
    }
}
