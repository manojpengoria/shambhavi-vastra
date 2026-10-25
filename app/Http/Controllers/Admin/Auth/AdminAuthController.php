<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Display the admin login page.
    */
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Authenticate an admin.
    */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::guard('admin')->attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();
        return redirect()->intended(
            route('admin.dashboard')
        );
    }

    /**
     * Log out the admin.
    */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')
            ->with('status', 'You have been logged out successfully.');
    }
}
