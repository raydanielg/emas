<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $messages = DB::table('support_messages')
            ->where('user_id', $userId)
            ->orderBy('created_at')
            ->get();
        return view('support.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate(['message'=>['required','string','max:2000']]);
        $userId = Auth::id();
        DB::table('support_messages')->insert([
            'user_id' => $userId,
            'role' => 'user',
            'message' => $request->input('message'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($request->wantsJson()) return response()->json(['ok'=>true]);
        return redirect()->route('support.index');
    }
}
