<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AdminPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $role = strtolower((string)($user->role ?? ''));
            if (!in_array($role, ['admin','superadmin'])) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
