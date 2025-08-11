<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $login = $request->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to authenticate using username or email
        if (Auth::attempt([$field => $login, 'password' => $request->password], true)) {
            $request->session()->regenerate();
            return redirect()->intended($this->roleRedirect(Auth::user()));
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function roleRedirect($user): string
    {
        $role = $user->role ?? '';
        switch ($role) {
            case 'headmaster':
                return '/headmaster';
            // Add more role-specific panels here as they are built
            // case 'admin': return '/admin';
            // case 'chairperson': return '/chair';
            default:
                return '/dashboard';
        }
    }
}
