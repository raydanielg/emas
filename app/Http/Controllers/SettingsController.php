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
            'avatar_choice' => ['nullable','string','max:255'],
        ]);

        // Filter out fields not present in DB yet (handles cases where migration hasn't run)
        $columns = Schema::getColumnListing('users');

        // Preset avatars whitelist (public assets)
        $presets = [
            'avatars/google/avatar1.svg',
            'avatars/google/avatar2.svg',
        ];

        // If user selected to clear avatar
        if (!empty($data['avatar_choice']) && $data['avatar_choice'] === 'none' && in_array('avatar_path', $columns, true)) {
            if ($user->avatar_path && str_starts_with($user->avatar_path, 'profiles/') && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = null;
        }

        // If user selected a preset, prefer it
        if (!empty($data['avatar_choice']) && in_array($data['avatar_choice'], $presets, true) && in_array('avatar_path', $columns, true)) {
            // Delete old stored file only if it was a storage profile file
            if ($user->avatar_path && str_starts_with($user->avatar_path, 'profiles/') && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $data['avatar_choice'];
        }

        // Otherwise handle uploaded file
        if (empty($data['avatar_path']) && $request->hasFile('avatar') && in_array('avatar_path', $columns, true)) {
            $path = $request->file('avatar')->store('profiles', 'public');
            if ($user->avatar_path && str_starts_with($user->avatar_path, 'profiles/') && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $path;
        }
        unset($data['avatar'], $data['avatar_choice']);

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
