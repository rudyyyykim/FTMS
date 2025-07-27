<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $maxAttempts = 5;
    protected $decayMinutes = 15;
    
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Throttle check
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Enhanced authentication check
        if (!$user) {
            $this->incrementLoginAttempts($request);
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // Case-insensitive status check
        if (!$user->isActive()) {
            return back()->withErrors(['email' => 'Your account is not active']);
        }

        // Password verification
        if (!Hash::check($credentials['password'], $user->password)) {
            $this->incrementLoginAttempts($request);
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // Rehash password if needed (uses model's setPasswordAttribute)
        if (Hash::needsRehash($user->password)) {
            $user->password = $credentials['password']; // Model's mutator will hash it
            $user->save();
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $user);
    }

    protected function authenticated(Request $request, $user)
    {
        switch ($user->role) {
            case 'Admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'Pka':
                return redirect()->intended(route('pka.dashboard'));
            case 'Staff':
                return redirect()->intended(route('staff.dashboard'));
            default:
                return redirect()->intended('/home');
        }
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts
        );
    }

    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes * 60
        );
    }

    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return back()->withErrors([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}