<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SimplePasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.simple-forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email'
        ], [
            'email.required' => 'Alamat emel diperlukan.',
            'email.email' => 'Format emel tidak sah.',
            'email.exists' => 'Emel tidak dijumpai dalam sistem.'
        ]);

        // Generate a simple token
        $token = Str::random(60);
        
        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Create reset URL
        $resetUrl = route('simple.password.reset', ['token' => $token, 'email' => $request->email]);

        // For simplicity, we'll just show the reset link on screen
        // In production, you would send this via email
        return back()->with('status', 'Pautan set semula kata laluan: ' . $resetUrl);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.simple-reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:user,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'Alamat emel diperlukan.',
            'email.email' => 'Format emel tidak sah.',
            'email.exists' => 'Emel tidak dijumpai dalam sistem.',
            'password.required' => 'Kata laluan diperlukan.',
            'password.min' => 'Kata laluan mestilah sekurang-kurangnya 8 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        // Check if token exists and is not expired (24 hours)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>', Carbon::now()->subHours(24))
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Token tidak sah atau telah tamat tempoh.']);
        }

        // Verify token (simple comparison for this implementation)
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Token tidak sah.']);
        }

        // Update user password (the User model's setPasswordAttribute will hash it automatically)
        $user = User::where('email', $request->email)->first();
        $user->password = $request->password; // Don't use Hash::make() here!
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Kata laluan berjaya dikemas kini!');
    }
}
