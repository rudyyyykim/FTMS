<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SimplePasswordChangeController extends Controller
{
    public function showChangeForm()
    {
        return view('auth.simple-change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'Alamat emel diperlukan.',
            'email.email' => 'Format emel tidak sah.',
            'email.exists' => 'Emel tidak dijumpai dalam sistem.',
            'current_password.required' => 'Kata laluan semasa diperlukan.',
            'new_password.required' => 'Kata laluan baru diperlukan.',
            'new_password.min' => 'Kata laluan baru mestilah sekurang-kurangnya 8 aksara.',
            'new_password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata laluan semasa tidak betul.']);
        }

        // Update password (the User model's setPasswordAttribute will hash it automatically)
        $user->password = $request->new_password; // Don't use Hash::make() here!
        $user->save();

        return back()->with('success', 'Kata laluan berjaya dikemas kini!');
    }
}
