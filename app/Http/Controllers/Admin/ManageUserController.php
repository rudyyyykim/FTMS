<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FileReturn;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ManageUserController extends Controller
{
    public function index()
    {
        // Check if user is Admin role
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Unauthorized. Only Admin users can manage users.');
        }
        
        $users = User::all();
        return view('Admin.manageUser', compact('users'));
    }

    public function store(Request $request)
    {
        // Check if user is Admin role
        if (auth()->user()->role !== 'Admin') {
            return response()->json([
                'error' => 'Unauthorized. Only Admin users can create users.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'icNumber' => [
                'required',
                'string',
                'unique:user,icNumber',
                'regex:/^[0-9]{6}-[0-9]{2}-[0-9]{4}$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:user,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Pegawai,Pka',
        ], [
            'icNumber.regex' => 'Nombor IC mesti dalam format XXXXXX-XX-XXXX (contoh: 021108-06-0076)',
            'email.regex' => 'Format emel tidak sah. Sila masukkan emel yang betul (contoh: nama@domain.com)',
            'email.email' => 'Format emel tidak sah',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'icNumber' => $request->icNumber,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'userStatus' => 'Aktif',
        ]);

        return response()->json([
            'message' => 'Pengguna baru berjaya ditambah.',
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Check if user is Admin role
        if (auth()->user()->role !== 'Admin') {
            return response()->json([
                'error' => 'Unauthorized. Only Admin users can update users.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:user,email,'.$user->userID.',userID',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:Admin,Pegawai,Pka',
            'userStatus' => 'required|in:Aktif,Tidak Aktif',
        ], [
            'email.regex' => 'Format emel tidak sah. Sila masukkan emel yang betul (contoh: nama@domain.com)',
            'email.email' => 'Format emel tidak sah',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'userStatus' => $request->userStatus,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Maklumat pengguna berjaya dikemaskini.',
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        // Check if user is Admin role
        if (auth()->user()->role !== 'Admin') {
            return response()->json([
                'error' => 'Unauthorized. Only Admin users can delete users.'
            ], 403);
        }

        // Prevent deleting yourself
        if ($user->userID == auth()->user()->userID) {
            return response()->json([
                'error' => 'Anda tidak boleh memadam akaun sendiri.'
            ], 403);
        }

        try {
            // Check if user has any associated file returns
            $fileReturnsCount = FileReturn::where('userID', $user->userID)->count();
            
            if ($fileReturnsCount > 0) {
                return response()->json([
                    'error' => 'Tidak boleh memadam pengguna ini kerana masih mempunyai ' . $fileReturnsCount . ' rekod pemulangan fail yang berkaitan. Sila pastikan tiada rekod yang berkaitan sebelum memadam pengguna.'
                ], 409);
            }

            $user->delete();

            return response()->json([
                'message' => 'Pengguna berjaya dipadam.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ralat berlaku ketika memadam pengguna: ' . $e->getMessage()
            ], 500);
        }
    }
}