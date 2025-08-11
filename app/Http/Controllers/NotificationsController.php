<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $q = DB::table('notifications')->where('user_id', $userId);
        if ($search = $request->query('q')) {
            $q->where(function($w) use ($search){
                $w->where('title','like',"%$search%")
                  ->orWhere('message','like',"%$search%");
            });
        }
        $notifications = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $n = DB::table('notifications')->where('user_id',$userId)->where('id',$id)->first();
        if (!$n) abort(404);
        if (is_null($n->read_at)) {
            DB::table('notifications')->where('id',$n->id)->update(['read_at'=>now()]);
        }
        return view('notifications.show', ['n'=>$n]);
    }

    public function latest()
    {
        $userId = Auth::id();
        $items = DB::table('notifications')
            ->where('user_id',$userId)
            ->orderByDesc('created_at')
            ->limit(5)->get();
        $unread = DB::table('notifications')->where('user_id',$userId)->whereNull('read_at')->count();
        return response()->json(['items'=>$items,'unread'=>$unread]);
    }
}
