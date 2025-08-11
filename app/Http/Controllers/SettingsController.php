<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'date_of_birth' => ['nullable','date'],
            'bank_number' => ['nullable','string','max:100'],
            'institution' => ['nullable','string','max:255'],
            'avatar' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:2048'],
        ]);

        // Filter out fields not present in DB yet (handles cases where migration hasn't run)
        $columns = Schema::getColumnListing('users');

        // Handle avatar upload only if avatar_path column exists
        if ($request->hasFile('avatar') && in_array('avatar_path', $columns, true)) {
            $path = $request->file('avatar')->store('avatars', 'public');
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $path;
        } else {
            unset($data['avatar']);
        }

        // Keep only keys that are actual columns
        $data = array_intersect_key($data, array_flip($columns));

        $user->fill($data);
        $user->save();
        return back()->with('status','Profile updated');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required','confirmed','min:6'],
        ]);
        $user = Auth::user();
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password'=>'Current password is incorrect']);
        }
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return back()->with('status','Password updated');
    }
}
