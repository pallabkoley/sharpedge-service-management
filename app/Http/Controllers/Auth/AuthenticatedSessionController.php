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
        // Authenticate user
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        // Clear intended redirect (VERY IMPORTANT)
        $request->session()->forget('url.intended');

        $user = Auth::user();

        // 🔐 Active check
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Account is inactive']);
        }

        // 🔐 Role safety
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        if (!$user->role) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Role not assigned']);
        }

        // 🎯 Role-based redirect (FINAL)
        return match ($user->role->name) {
            'admin'      => redirect()->route('admin.dashboard'),
            'marketing'  => redirect()->route('marketing.dashboard'),
            'backoffice' => redirect()->route('backoffice.dashboard'),
            'client'     => redirect()->route('client.dashboard'),
            default      => tap(Auth::logout(), fn () =>
                redirect()->route('login')
                    ->withErrors(['email' => 'Unauthorized role'])
            ),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
