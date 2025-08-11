<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeadmasterSubjectsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch subjects
        $subjects = collect();
        if (Schema::hasTable('subjects')) {
            $q = DB::table('subjects');
            $select = [];
            foreach (['id','name','code','teacher_id','school_id','created_at'] as $c) {
                if (Schema::hasColumn('subjects',$c)) $select[] = $c;
            }
            if (!empty($select)) $q->select($select);
            $subjects = $q->orderBy(Schema::hasColumn('subjects','name') ? 'name' : 'id')->get();
        }

        // Fetch teachers
        $teachers = collect();
        if (Schema::hasTable('users')) {
            $uq = DB::table('users');
            $uselect = [];
            foreach (['id','name','full_name','email','role','school_id'] as $c) if (Schema::hasColumn('users',$c)) $uselect[] = $c;
            if (!empty($uselect)) $uq->select($uselect);
            // Try to filter by role teacher if column exists
            if (Schema::hasColumn('users','role')) { $uq->where('role','like','%teacher%'); }
            $teachers = $uq->orderBy(Schema::hasColumn('users','name') ? 'name' : 'id')->get()->map(function($u){
                $u->display_name = $u->name ?? $u->full_name ?? $u->email ?? ('User '.$u->id);
                return $u;
            });
        }

        // Map teacher names for table display
        $teacherIndex = $teachers->keyBy('id');
        $subjects = $subjects->map(function($s) use ($teacherIndex){
            $s->teacher_name = ($s->teacher_id ?? null) && isset($teacherIndex[$s->teacher_id])
                ? ($teacherIndex[$s->teacher_id]->display_name)
                : null;
            return $s;
        });

        return view('headmaster.subjects.index', [
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('subjects')) { return back()->with('error','Subjects table not found'); }
        $name = trim((string)$request->input('name'));
        $code = strtoupper(trim((string)$request->input('code')));
        $teacherId = $request->input('teacher_id');
        if ($name === '' || $code === '') { return back()->with('error','Name and code are required'); }

        $data = [];
        if (Schema::hasColumn('subjects','name')) $data['name'] = $name;
        if (Schema::hasColumn('subjects','code')) $data['code'] = $code;
        if (Schema::hasColumn('subjects','teacher_id')) $data['teacher_id'] = $teacherId ?: null;
        if (Schema::hasColumn('subjects','created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('subjects','updated_at')) $data['updated_at'] = now();

        try {
            DB::table('subjects')->insert($data);
            return back()->with('success','Subject registered');
        } catch (\Throwable $e) {
            return back()->with('error','Failed to register subject');
        }
    }

    public function assignTeacher(Request $request, $id)
    {
        if (!Schema::hasTable('subjects')) { return response()->json(['ok'=>false,'message'=>'Subjects table not found'],404); }
        $teacherId = $request->input('teacher_id');
        $data = [];
        if (Schema::hasColumn('subjects','teacher_id')) $data['teacher_id'] = $teacherId ?: null;
        if (Schema::hasColumn('subjects','updated_at')) $data['updated_at'] = now();
        try {
            DB::table('subjects')->where('id',$id)->update($data);
            return response()->json(['ok'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>'Failed to assign teacher'],500);
        }
    }

    public function show($id)
    {
        if (!Schema::hasTable('subjects')) { abort(404); }
        $select = [];
        foreach (['id','name','code','teacher_id','school_id','created_at','updated_at'] as $c) if (Schema::hasColumn('subjects',$c)) $select[] = $c;
        $sub = DB::table('subjects')->when(!empty($select), fn($q)=>$q->select($select))->where('id',$id)->first();
        if (!$sub) abort(404);

        $teacher = null;
        if (Schema::hasTable('users') && isset($sub->teacher_id) && $sub->teacher_id) {
            $uselect = [];
            foreach (['id','name','full_name','email'] as $c) if (Schema::hasColumn('users',$c)) $uselect[] = $c;
            $teacher = DB::table('users')->when(!empty($uselect), fn($q)=>$q->select($uselect))->where('id',$sub->teacher_id)->first();
            if ($teacher) {
                $teacher->display_name = $teacher->name ?? $teacher->full_name ?? $teacher->email ?? ('User '.$teacher->id);
            }
        }

        return view('headmaster.subjects.show', [
            'subject' => $sub,
            'teacher' => $teacher,
        ]);
    }

    public function edit($id)
    {
        if (!Schema::hasTable('subjects')) { abort(404); }
        $scols = [];
        foreach (['id','name','code','teacher_id'] as $c) if (Schema::hasColumn('subjects',$c)) $scols[] = $c;
        $subject = DB::table('subjects')->when(!empty($scols), fn($q)=>$q->select($scols))->where('id',$id)->first();
        if (!$subject) abort(404);

        $teachers = collect();
        if (Schema::hasTable('users')) {
            $uq = DB::table('users');
            $uselect = [];
            foreach (['id','name','full_name','email','role'] as $c) if (Schema::hasColumn('users',$c)) $uselect[] = $c;
            if (!empty($uselect)) $uq->select($uselect);
            if (Schema::hasColumn('users','role')) { $uq->where('role','like','%teacher%'); }
            $teachers = $uq->orderBy(Schema::hasColumn('users','name') ? 'name' : 'id')->get()->map(function($u){
                $u->display_name = $u->name ?? $u->full_name ?? $u->email ?? ('User '.$u->id);
                return $u;
            });
        }

        return view('headmaster.subjects.edit', [
            'subject' => $subject,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Schema::hasTable('subjects')) { return back()->with('error','Subjects table not found'); }
        $name = trim((string)$request->input('name'));
        $code = strtoupper(trim((string)$request->input('code')));
        $teacherId = $request->input('teacher_id');
        if ($name === '' || $code === '') { return back()->with('error','Name and code are required'); }

        $data = [];
        if (Schema::hasColumn('subjects','name')) $data['name'] = $name;
        if (Schema::hasColumn('subjects','code')) $data['code'] = $code;
        if (Schema::hasColumn('subjects','teacher_id')) $data['teacher_id'] = $teacherId ?: null;
        if (Schema::hasColumn('subjects','updated_at')) $data['updated_at'] = now();
        try {
            DB::table('subjects')->where('id',$id)->update($data);
            return redirect()->route('headmaster.subjects.show',$id)->with('success','Subject updated');
        } catch (\Throwable $e) {
            return back()->with('error','Failed to update subject');
        }
    }

    public function destroy($id)
    {
        if (!Schema::hasTable('subjects')) { return back()->with('error','Subjects table not found'); }
        try {
            DB::table('subjects')->where('id',$id)->delete();
            return redirect()->route('headmaster.subjects.index')->with('success','Subject deleted');
        } catch (\Throwable $e) {
            return back()->with('error','Failed to delete subject');
        }
    }
}
