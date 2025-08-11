<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class HeadmasterTeachersController extends Controller
{
    public function index(Request $request)
    {
        // Fetch teachers
        $teachers = collect();
        if (Schema::hasTable('users')) {
            $uq = DB::table('users');
            $select = [];
            foreach (['id','name','full_name','first_name','middle_name','last_name','email','phone','bank_number','role','subject_id','created_at'] as $c) {
                if (Schema::hasColumn('users',$c)) $select[] = $c;
            }
            
            if (!empty($select)) $uq->select($select);
            if (Schema::hasColumn('users','role')) {
                $uq->where('role','like','%teacher%');
            }
            $teachers = $uq->orderBy(Schema::hasColumn('users','name') ? 'name' : 'id')->get()->map(function($u){
                $nameParts = [];
                if (isset($u->first_name)) $nameParts[] = $u->first_name;
                if (isset($u->middle_name)) $nameParts[] = $u->middle_name;
                if (isset($u->last_name)) $nameParts[] = $u->last_name;
                $display = trim(implode(' ', array_filter($nameParts))) ?: ($u->name ?? $u->full_name ?? $u->email ?? ('User '.$u->id));
                $u->display_name = $display;
                return $u;
            });
        }

        // Fetch subjects for assignment
        $subjects = collect();
        if (Schema::hasTable('subjects')) {
            $sq = DB::table('subjects');
            $sselect = [];
            foreach (['id','name','code','teacher_id'] as $c) if (Schema::hasColumn('subjects',$c)) $sselect[] = $c;
            if (!empty($sselect)) $sq->select($sselect);
            $subjects = $sq->orderBy(Schema::hasColumn('subjects','name') ? 'name' : 'id')->get();
        }

        // Map each teacher's subject name by subjects.teacher_id link or users.subject_id
        $subjectsByTeacher = $subjects->whereNotNull('teacher_id')->keyBy('teacher_id');
        $subjectsById = $subjects->keyBy('id');
        $teachers = $teachers->map(function($t) use ($subjectsByTeacher, $subjectsById){
            $bySubjectsTable = isset($subjectsByTeacher[$t->id]) ? $subjectsByTeacher[$t->id] : null;
            $byUsersColumn = (isset($t->subject_id) && $t->subject_id && isset($subjectsById[$t->subject_id])) ? $subjectsById[$t->subject_id] : null;
            $sub = $bySubjectsTable ?: $byUsersColumn;
            $t->subject_name = $sub->name ?? null;
            $t->subject_code = $sub->code ?? null;
            $t->subject_assigned_id = $sub->id ?? null;
            return $t;
        });

        return view('headmaster.teachers.index', [
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('users')) { return back()->with('error','Users table not found'); }
        $first = trim((string)$request->input('first_name'));
        $middle = trim((string)$request->input('middle_name'));
        $last = trim((string)$request->input('last_name'));
        $phone = trim((string)$request->input('phone'));
        $bank = trim((string)$request->input('bank_number'));
        $email = trim((string)$request->input('email', ''));
        $subjectId = $request->input('subject_id');

        if ($first === '' || $last === '') { return back()->with('error','First and last name are required'); }

        $data = [];
        $fullName = trim($first.' '.($middle ? $middle.' ' : '').$last);
        if (Schema::hasColumn('users','first_name')) $data['first_name'] = $first;
        if (Schema::hasColumn('users','middle_name')) $data['middle_name'] = $middle ?: null;
        if (Schema::hasColumn('users','last_name')) $data['last_name'] = $last;
        if (Schema::hasColumn('users','name')) $data['name'] = $fullName;
        if (Schema::hasColumn('users','full_name')) $data['full_name'] = $fullName;
        if (Schema::hasColumn('users','phone')) $data['phone'] = $phone ?: null;
        if (Schema::hasColumn('users','bank_number')) $data['bank_number'] = $bank ?: null;
        if (Schema::hasColumn('users','role')) $data['role'] = 'teacher';
        if (Schema::hasColumn('users','email')) $data['email'] = $email ?: null;
        if (Schema::hasColumn('users','password')) $data['password'] = Hash::make('Pass@123');
        if (Schema::hasColumn('users','created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('users','updated_at')) $data['updated_at'] = now();

        try {
            $teacherId = DB::table('users')->insertGetId($data);
            // Link subject if provided
            if ($subjectId) {
                if (Schema::hasTable('subjects') && Schema::hasColumn('subjects','teacher_id')) {
                    // clear previous assignment for this teacher
                    DB::table('subjects')->where('teacher_id', $teacherId)->update(['teacher_id' => null]);
                    DB::table('subjects')->where('id', $subjectId)->update(['teacher_id' => $teacherId, 'updated_at' => now()]);
                }
                if (Schema::hasColumn('users','subject_id')) {
                    DB::table('users')->where('id',$teacherId)->update(['subject_id'=>$subjectId, 'updated_at'=>now()]);
                }
            }
            return back()->with('success','Teacher added');
        } catch (\Throwable $e) {
            return back()->with('error','Failed to add teacher');
        }
    }

    // Proposals
    public function proposalsIndex(Request $request)
    {
        $proposals = collect();
        if (Schema::hasTable('teacher_proposals')) {
            $pq = DB::table('teacher_proposals');
            $select = [];
            foreach (['id','title','notes','status','created_at'] as $c) if (Schema::hasColumn('teacher_proposals',$c)) $select[] = $c;
            if (!empty($select)) $pq->select($select);
            $pq->orderBy(Schema::hasColumn('teacher_proposals','created_at') ? 'created_at' : 'id','desc');
            $proposals = $pq->get();

            // counts per proposal
            if (Schema::hasTable('proposal_teacher')) {
                $links = DB::table('proposal_teacher')->select('proposal_id','status')->get();
                $grouped = $links->groupBy('proposal_id');
                $proposals = $proposals->map(function($p) use ($grouped){
                    $g = $grouped->get($p->id, collect());
                    $p->count_total = $g->count();
                    $p->count_selected = $g->where('status','selected')->count();
                    $p->count_rejected = $g->where('status','rejected')->count();
                    $p->count_pending = $g->where('status',null)->count() + $g->where('status','pending')->count();
                    return $p;
                });
            }
        }

        // teachers list for modal
        $teachers = collect();
        if (Schema::hasTable('users')) {
            $tq = DB::table('users');
            $tsel = [];
            foreach (['id','first_name','middle_name','last_name','name','full_name','email'] as $c) if (Schema::hasColumn('users',$c)) $tsel[] = $c;
            if (!empty($tsel)) $tq->select($tsel);
            if (Schema::hasColumn('users','role')) $tq->where('role','like','%teacher%');
            $teachers = $tq->orderBy(Schema::hasColumn('users','name') ? 'name' : 'id')->get()->map(function($u){
                $name = trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? ''));
                $u->display_name = $name !== '' ? $name : ($u->name ?? $u->full_name ?? $u->email ?? ('User '.$u->id));
                return $u;
            });
        }

        return view('headmaster.teachers.proposals.index', [
            'proposals' => $proposals,
            'teachers' => $teachers,
        ]);
    }

    public function proposalsStore(Request $request)
    {
        if (!Schema::hasTable('teacher_proposals')) { return back()->with('error','Proposals table not found'); }
        $title = trim((string)$request->input('title'));
        $notes = trim((string)$request->input('notes'));
        $teacherIds = $request->input('teacher_ids', []);
        if ($title === '') { return back()->with('error','Title is required'); }

        try {
            $pid = DB::table('teacher_proposals')->insertGetId([
                'title' => $title,
                'notes' => $notes ?: null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (Schema::hasTable('proposal_teacher') && is_array($teacherIds)) {
                $rows = [];
                foreach ($teacherIds as $tid) {
                    $rows[] = [
                        'proposal_id' => $pid,
                        'teacher_id' => (int)$tid,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($rows)) DB::table('proposal_teacher')->insert($rows);
            }
            return redirect()->route('headmaster.teachers.proposals')->with('success','Proposal created');
        } catch (\Throwable $e) {
            return back()->with('error','Failed to create proposal');
        }
    }

    public function proposalsShow($id)
    {
        if (!Schema::hasTable('teacher_proposals')) { abort(404); }
        $pcols = [];
        foreach (['id','title','notes','status','created_at'] as $c) if (Schema::hasColumn('teacher_proposals',$c)) $pcols[] = $c;
        $proposal = DB::table('teacher_proposals')->when(!empty($pcols), fn($q)=>$q->select($pcols))->where('id',$id)->first();
        if (!$proposal) abort(404);

        $items = collect();
        if (Schema::hasTable('proposal_teacher')) {
            $links = DB::table('proposal_teacher')->where('proposal_id',$id)->get();
            $items = $links;
            // attach teacher names
            if (Schema::hasTable('users')) {
                $teachers = DB::table('users')->select('id','first_name','middle_name','last_name','name','full_name','email')->get()->keyBy('id');
                $items = $items->map(function($row) use ($teachers){
                    $u = $teachers->get($row->teacher_id);
                    $name = $u ? trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? '')) : '';
                    $row->teacher_name = $name !== '' ? $name : ($u->name ?? $u->full_name ?? $u->email ?? ('User '.$row->teacher_id));
                    return $row;
                });
            }
        }

        return view('headmaster.teachers.proposals.show', [
            'proposal' => $proposal,
            'items' => $items,
        ]);
    }

    // Selected for Marking
    public function selectedIndex(Request $request)
    {
        $selections = collect();
        if (Schema::hasTable('teacher_selections')) {
            $q = DB::table('teacher_selections');
            $cols = [];
            foreach (['id','title','notes','status','letter_generated_at','created_at'] as $c) if (Schema::hasColumn('teacher_selections',$c)) $cols[]=$c;
            if (!empty($cols)) $q->select($cols);
            $q->orderBy(Schema::hasColumn('teacher_selections','created_at') ? 'created_at' : 'id','desc');
            $selections = $q->get();
            if (Schema::hasTable('selection_teacher')) {
                $links = DB::table('selection_teacher')->select('selection_id','status')->get()->groupBy('selection_id');
                $selections = $selections->map(function($s) use ($links){
                    $g = $links->get($s->id, collect());
                    $s->count_total = $g->count();
                    $s->count_selected = $g->where('status','selected')->count();
                    $s->count_rejected = $g->where('status','rejected')->count();
                    $s->count_pending = $g->where('status','pending')->count();
                    return $s;
                });
            }
        }
        return view('headmaster.teachers.selected.index', [ 'selections' => $selections ]);
    }

    public function selectedShow($id)
    {
        if (!Schema::hasTable('teacher_selections')) abort(404);
        $cols=[]; foreach(['id','title','notes','status','letter_generated_at','created_at'] as $c) if (Schema::hasColumn('teacher_selections',$c)) $cols[]=$c;
        $selection = DB::table('teacher_selections')->when(!empty($cols), fn($q)=>$q->select($cols))->where('id',$id)->first();
        if (!$selection) abort(404);

        $items = collect();
        if (Schema::hasTable('selection_teacher')) {
            $items = DB::table('selection_teacher')->where('selection_id',$id)->get();
            if (Schema::hasTable('users')) {
                $teachers = DB::table('users')->select('id','first_name','middle_name','last_name','name','full_name','email')->get()->keyBy('id');
                $items = $items->map(function($row) use ($teachers){
                    $u = $teachers->get($row->teacher_id);
                    $name = $u ? trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? '')) : '';
                    $row->teacher_name = $name !== '' ? $name : ($u->name ?? $u->full_name ?? $u->email ?? ('User '.$row->teacher_id));
                    return $row;
                });
            }
        }

        return view('headmaster.teachers.selected.show', [ 'selection' => $selection, 'items' => $items ]);
    }

    public function selectedGenerateLetter(Request $request, $id)
    {
        if (!Schema::hasTable('teacher_selections')) abort(404);
        $selection = DB::table('teacher_selections')->where('id',$id)->first();
        if (!$selection) abort(404);
        $items = collect();
        if (Schema::hasTable('selection_teacher')) $items = DB::table('selection_teacher')->where('selection_id',$id)->get();
        if (Schema::hasTable('users')) {
            $teachers = DB::table('users')->select('id','first_name','middle_name','last_name','name','full_name','email')->get()->keyBy('id');
            $items = $items->map(function($row) use ($teachers){
                $u = $teachers->get($row->teacher_id);
                $name = $u ? trim(($u->first_name ?? '').' '.($u->middle_name ?? '').' '.($u->last_name ?? '')) : '';
                $row->teacher_name = $name !== '' ? $name : ($u->name ?? $u->full_name ?? $u->email ?? ('User '.$row->teacher_id));
                return $row;
            });
        }

        // Try AI generation
        $html = null;
        try {
            if (class_exists('App\\Services\\GeminiService')) {
                $html = \App\Services\GeminiService::generateOfficialSelectionLetter($selection, $items);
            }
        } catch (\Throwable $e) {
            $html = null; // fall back below
        }

        if (!$html) {
            // Fallback formatted HTML letter (policy-compliant template)
            $date = now()->format('d M Y');
            $rows = $items->map(function($r){
                $role = ucfirst($r->role ?? ''); $as = $r->assigned_as ?? ''; $st = ucfirst($r->status ?? 'selected');
                return "<tr><td>".e($r->teacher_name)."</td><td>".e($role)."</td><td>".e($as)."</td><td>".e($st)."</td></tr>";
            })->implode('');
            $html = "<div style='font-family:Segoe UI,Arial,sans-serif; color:#111'>".
                "<div style='text-align:center; margin-bottom:12px'>".
                "<div style='font-weight:700'>THE UNITED REPUBLIC OF TANZANIA</div>".
                "<div>MINISTRY OF EDUCATION, SCIENCE AND TECHNOLOGY</div>".
                "<div>THE NATIONAL EXAMINATIONS COUNCIL OF TANZANIA (NECTA)</div>".
                "<div style='margin-top:6px; font-size:13px;'>Official Appointment Letter for Marking Exercise</div>".
                "</div>".
                "<div style='margin-bottom:10px; font-size:14px'>Ref: Selection #".e($selection->id)." | Date: ".$date."</div>".
                "<p>We hereby inform you that the following teachers have been selected to participate in the examination marking exercise as indicated below:</p>".
                "<table width='100%' cellspacing='0' cellpadding='6' style='border-collapse:collapse; font-size:13px'>".
                "<thead><tr style='background:#f2f2f2'><th align='left' style='border:1px solid #ddd'>Teacher</th><th align='left' style='border:1px solid #ddd'>Role</th><th align='left' style='border:1px solid #ddd'>Assigned As</th><th align='left' style='border:1px solid #ddd'>Status</th></tr></thead>".
                "<tbody>".$rows."</tbody></table>".
                (!empty($selection->notes) ? ("<p style='margin-top:10px'><strong>Notes:</strong> ".e($selection->notes)."</p>") : '') .
                "<p style='margin-top:16px'>Kindly adhere to all instructions issued regarding conduct and timelines of the marking exercise. Failure to comply may result in replacement.</p>".
                "<div style='margin-top:20px'>".
                "<div>Signed,</div><div style='margin-top:30px; font-weight:600'>Headmaster</div>".
                "</div></div>";
        }

        DB::table('teacher_selections')->where('id',$id)->update([
            'letter_html' => $html,
            'letter_generated_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('headmaster.teachers.selected.letter', $id)->with('success','Letter generated');
    }

    public function selectedViewLetter($id)
    {
        if (!Schema::hasTable('teacher_selections')) abort(404);
        $selection = DB::table('teacher_selections')->where('id',$id)->first();
        if (!$selection) abort(404);
        return view('headmaster.teachers.selected.letter', [ 'selection' => $selection ]);
    }

    public function assignSubject(Request $request, $id)
    {
        $subjectId = $request->input('subject_id');
        if (!$subjectId) return response()->json(['ok'=>false,'message'=>'No subject selected'], 422);

        try {
            if (Schema::hasTable('subjects') && Schema::hasColumn('subjects','teacher_id')) {
                // clear current subject that this teacher holds
                DB::table('subjects')->where('teacher_id', $id)->update(['teacher_id' => null, 'updated_at' => now()]);
                // set new
                DB::table('subjects')->where('id', $subjectId)->update(['teacher_id' => $id, 'updated_at' => now()]);
            }
            if (Schema::hasTable('users') && Schema::hasColumn('users','subject_id')) {
                DB::table('users')->where('id',$id)->update(['subject_id'=>$subjectId, 'updated_at'=>now()]);
            }
            return response()->json(['ok'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'message'=>'Failed to assign subject'], 500);
        }
    }
}
