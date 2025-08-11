<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $userId = Auth::id();
        // Accept either text message or attachment/voice
        $request->validate([
            'message' => ['nullable','string','max:2000'],
            'attachment' => ['nullable','file','max:10240'], // 10 MB
            'voice' => ['nullable','file','mimetypes:audio/webm,audio/ogg,audio/mpeg','max:10240'],
        ]);

        $message = $request->input('message');
        $attachmentPath = null; $attachmentName = null; $attachmentMime = null; $attachmentSize = null; $attachmentType = null;

        // Determine file input (voice has priority if present)
        $file = $request->file('voice') ?: $request->file('attachment');
        if ($file) {
            $attachmentMime = $file->getMimeType();
            $attachmentSize = $file->getSize();
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = str_starts_with($attachmentMime, 'image/') ? 'image' : (str_starts_with($attachmentMime, 'audio/') ? 'audio' : 'file');
            $attachmentPath = $file->store('support', 'public');
        }

        if (!$message && !$attachmentPath) {
            return back()->withErrors(['message' => 'Please write a message or attach a file.']);
        }

        DB::table('support_messages')->insert([
            'user_id' => $userId,
            'role' => 'user',
            'message' => $message,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
            'attachment_type' => $attachmentType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($request->wantsJson()) return response()->json(['ok'=>true]);
        return redirect()->route('support.index');
    }
}
