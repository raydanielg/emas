<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HeadmasterRequestsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Assuming role middleware exists; if not, auth-only is fine for now.
        if (method_exists($this, 'middleware')) {
            // $this->middleware('role:headmaster'); // uncomment if available
        }
    }

    public function pending()
    {
        $userId = Auth::id();
        $items = [];
        if (Schema::hasTable('headmaster_requests')) {
            $items = DB::table('headmaster_requests')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }
        return view('headmaster.requests.pending', compact('items'));
    }

    public function approved()
    {
        $userId = Auth::id();
        $items = [];
        if (Schema::hasTable('headmaster_requests')) {
            $items = DB::table('headmaster_requests')
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->orderByDesc('updated_at')
                ->limit(50)
                ->get();
        }
        return view('headmaster.requests.approved', compact('items'));
    }

    public function needApproval()
    {
        $userId = Auth::id();
        $items = [];
        if (Schema::hasTable('headmaster_requests')) {
            $items = DB::table('headmaster_requests')
                ->where('user_id', $userId)
                ->where('status', 'need_approval')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }
        return view('headmaster.requests.need-approval', compact('items'));
    }

    // Reports: Create Request (student count)
    public function create(Request $request)
    {
        $userId = Auth::id();

        // Load assigned schools (either direct user_id or via assignment table)
        $schools = collect();
        if (Schema::hasTable('user_school_assignments') && Schema::hasTable('schools')) {
            $q = DB::table('user_school_assignments as usa')
                ->join('schools','schools.id','=','usa.school_id')
                ->where('usa.user_id', $userId);
            $sel = [];
            foreach (['schools.id','schools.name','schools.code'] as $c) {
                [$t,$col] = explode('.', $c);
                if (Schema::hasColumn($t,$col)) $sel[] = $c;
            }
            if (!empty($sel)) $q->select($sel);
            $schools = $q->orderBy('schools.name')->get();
        } elseif (Schema::hasTable('schools')) {
            $schools = DB::table('schools')
                ->select('id','name','code')
                ->when(Schema::hasColumn('schools','user_id'), fn($q)=>$q->where('user_id',$userId))
                ->orderBy('name')
                ->limit(200)
                ->get();
        }

        // Compute distinct class levels from students table
        $classLevels = collect();
        if (Schema::hasTable('students')) {
            $cols = DB::getSchemaBuilder()->getColumnListing('students');
            $classCol = null;
            foreach (['class_level','class','form','grade'] as $c) {
                if (in_array($c,$cols,true)) { $classCol = $c; break; }
            }
            if ($classCol) {
                $sq = DB::table('students');
                if ($schools->count() && in_array('school_id',$cols,true)) {
                    $sq->whereIn('school_id', $schools->pluck('id')->all());
                }
                $classLevels = $sq->whereNotNull($classCol)
                    ->select($classCol.' as level')
                    ->distinct()
                    ->orderBy($classCol)
                    ->pluck('level');
            }
        }

        // Existing requests list for table
        $requests = collect();
        if (Schema::hasTable('headmaster_requests')) {
            $rq = DB::table('headmaster_requests')->where('user_id', $userId);
            if (Schema::hasTable('schools')) {
                $rq->leftJoin('schools','schools.id','=','headmaster_requests.school_id');
                $rsel = ['headmaster_requests.*'];
                if (Schema::hasColumn('schools','name')) $rsel[] = 'schools.name as school_name';
                if (Schema::hasColumn('schools','code')) $rsel[] = 'schools.code as school_code';
                $rq->select($rsel);
            }
            $requests = $rq->orderByDesc('created_at')->limit(100)->get();
        }

        return view('headmaster.reports.request-create', [
            'schools' => $schools,
            'classLevels' => $classLevels,
            'requests' => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required','string','max:100'],
            'message' => ['required','string','max:2000'],
            'school_id' => ['nullable','integer'],
        ]);

        $payload = [
            'user_id' => Auth::id(),
            'type' => $data['category'],
            'quantity' => null,
            'school_id' => $data['school_id'] ?? null,
            'comment' => $data['message'],
            'status' => 'need_approval',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasTable('headmaster_requests')) {
            // Column-safe insert
            $row = [];
            $cols = DB::getSchemaBuilder()->getColumnListing('headmaster_requests');
            foreach ($payload as $k=>$v) { if (in_array($k, $cols, true)) $row[$k] = $v; }
            // Minimal required
            if (!isset($row['status']) && in_array('status',$cols,true)) $row['status'] = 'need_approval';
            DB::table('headmaster_requests')->insert($row);
        } elseif (Schema::hasTable('support_messages')) {
            // Fallback: log via support_messages
            DB::table('support_messages')->insert([
                'user_id' => Auth::id(),
                'role' => 'user',
                'message' => '[REQUEST]['.$data['category'].'] school_id='.(string)($data['school_id'] ?? 'null').' msg='.$data['message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('headmaster.requests.need_approval')->with('status','Request submitted for approval.');
    }

    // Reports: Rollback Request
    public function rollbackCreate()
    {
        $schools = collect();
        if (Schema::hasTable('user_school_assignments') && Schema::hasTable('schools')) {
            $q = DB::table('user_school_assignments as usa')
                ->join('schools','schools.id','=','usa.school_id')
                ->where('usa.user_id', Auth::id());
            $sel = [];
            foreach (['schools.id','schools.name','schools.code'] as $c) {
                [$t,$col] = explode('.', $c);
                if (Schema::hasColumn($t,$col)) $sel[] = $c;
            }
            if (!empty($sel)) $q->select($sel);
            $schools = $q->orderBy('schools.name')->get();
        }
        return view('headmaster.reports.request-rollback', compact('schools'));
    }

    public function rollbackStore(Request $request)
    {
        $data = $request->validate([
            'reason' => ['required','string','max:1000'],
            'school_id' => ['nullable','integer'],
            'target' => ['nullable','string','max:255'], // e.g., admission no, batch id
        ]);

        $payload = [
            'user_id' => Auth::id(),
            'type' => 'rollback',
            'target' => $data['target'] ?? null,
            'comment' => $data['reason'],
            'school_id' => $data['school_id'] ?? null,
            'status' => 'need_approval',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasTable('headmaster_requests')) {
            $row = [];
            $cols = DB::getSchemaBuilder()->getColumnListing('headmaster_requests');
            foreach ($payload as $k=>$v) { if (in_array($k, $cols, true)) $row[$k] = $v; }
            if (!isset($row['status']) && in_array('status',$cols,true)) $row['status'] = 'need_approval';
            DB::table('headmaster_requests')->insert($row);
        } elseif (Schema::hasTable('support_messages')) {
            DB::table('support_messages')->insert([
                'user_id' => Auth::id(),
                'role' => 'user',
                'message' => '[REQUEST][ROLLBACK] target='.($data['target'] ?? '').' school_id='.(string)($data['school_id'] ?? 'null').' reason='.$data['reason'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('headmaster.requests.need_approval')->with('status','Rollback request submitted for approval.');
    }

    public function show($id)
    {
        if (!Schema::hasTable('headmaster_requests')) abort(404);
        $q = DB::table('headmaster_requests')->where('id', $id)->where('user_id', Auth::id());
        if (Schema::hasTable('schools')) {
            $q->leftJoin('schools','schools.id','=','headmaster_requests.school_id');
            $sel = ['headmaster_requests.*'];
            if (Schema::hasColumn('schools','name')) $sel[] = 'schools.name as school_name';
            if (Schema::hasColumn('schools','code')) $sel[] = 'schools.code as school_code';
            $q->select($sel);
        }
        $item = $q->first();
        if (!$item) abort(404);
        return view('headmaster.reports.request-show', compact('item'));
    }

    public function cancel($id)
    {
        if (!Schema::hasTable('headmaster_requests')) return back();
        $tbl = 'headmaster_requests';
        $cols = DB::getSchemaBuilder()->getColumnListing($tbl);
        $q = DB::table($tbl)->where('id',$id)->where('user_id', Auth::id());
        // Only cancel if not approved
        if (in_array('status',$cols,true)) {
            $row = $q->first();
            if ($row && isset($row->status) && $row->status !== 'approved') {
                $upd = [];
                if (in_array('status',$cols,true)) $upd['status'] = 'cancelled';
                if (in_array('updated_at',$cols,true)) $upd['updated_at'] = now();
                if (!empty($upd)) $q->update($upd);
            }
        }
        return back()->with('status','Request cancelled.');
    }
}
