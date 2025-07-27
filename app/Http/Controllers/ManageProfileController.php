<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ManageProfileController extends Controller
{
    public function index()
    {
        // Get current authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }
        
        return view('admin.manageProfile', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }
        
        $userID = $user->userID;
        
        // Validation rules
        $request->validate([
            'username' => 'required|string|max:100|unique:user,username,' . $userID . ',userID',
            'icNumber' => [
                'required',
                'string',
                'min:14',
                'max:14',
                'regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/',
                'unique:user,icNumber,' . $userID . ',userID'
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:user,email,' . $userID . ',userID'
            ],
            'password' => 'nullable|confirmed|min:8',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'icNumber.regex' => 'Nombor IC mesti dalam format XXXXXX-XX-XXXX (contoh: 021108-06-0076)',
            'icNumber.min' => 'Nombor IC mesti 14 aksara termasuk tanda sempang',
            'icNumber.max' => 'Nombor IC mesti 14 aksara termasuk tanda sempang',
            'email.regex' => 'Format emel tidak sah. Gunakan format yang betul seperti nama@domain.com',
            'email.email' => 'Sila masukkan alamat emel yang sah'
        ]);
        
        $updateData = [
            'username' => $request->username,
            'icNumber' => $request->icNumber,
            'email' => $request->email,
            'updated_at' => now()
        ];
        
        // Handle password update
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        // Handle profile picture upload
        if ($request->hasFile('profilePicture')) {
            // Delete old profile picture if exists
            if ($user->profilePicture && file_exists(public_path('images/' . $user->profilePicture))) {
                unlink(public_path('images/' . $user->profilePicture));
            }
            
            // Store new profile picture
            $file = $request->file('profilePicture');
            $filename = $userID . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $updateData['profilePicture'] = $filename;
        }
        
        // Update user profile
        $updated = DB::table('user')
            ->where('userID', $userID)
            ->update($updateData);
        
        if ($updated) {
            return redirect()->route('admin.manageProfile')
                ->with('success', 'Profil berjaya dikemaskini!');
        } else {
            return redirect()->back()
                ->with('error', 'Ralat semasa mengemaskini profil.')
                ->withInput();
        }
    }
    
    public function removeProfilePicture()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }
        
        if ($user->profilePicture) {
            // Delete the file from storage
            if (file_exists(public_path('images/' . $user->profilePicture))) {
                unlink(public_path('images/' . $user->profilePicture));
            }
            
            // Update database
            DB::table('user')
                ->where('userID', $user->userID)
                ->update(['profilePicture' => null, 'updated_at' => now()]);
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }
}
