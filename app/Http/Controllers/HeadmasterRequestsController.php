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
}
