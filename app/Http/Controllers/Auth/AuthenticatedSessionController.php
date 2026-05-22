<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user?->role ?? null;

        // Mapping dashboard per role. Fallback ke admin.dashboard kalau route
        // role-specific belum terdaftar (defensive).
        $roleDashboard = [
            'superadmin' => 'admin.dashboard',
            'administration' => 'admin.bendahara.dashboard',
            'bendahara' => 'admin.bendahara.dashboard',
            'headmaster' => 'admin.headmaster.dashboard',
            'teacher' => 'admin.teacher.dashboard',
        ];

        if (isset($roleDashboard[$role]) && \Illuminate\Support\Facades\Route::has($roleDashboard[$role])) {
            $default = route($roleDashboard[$role], absolute: false);
        } elseif (isset($roleDashboard[$role])) {
            $default = route('admin.dashboard', absolute: false);
        } else {
            $default = route('dashboard', absolute: false);
        }

        return redirect()->intended($default);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
