<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Admin_information;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function adminRegistration(Request $request,Admin $admins,Admin_information $adminInformation) {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $adminAccount = $admins->create($validatedData);
        
        $adminInfo = $adminInformation->create([
            'admin_id' => $adminAccount->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address
        ]);


        return response()->json([
            'message' => 'Successfully Create Admin',
            'admin' => $adminAccount,
            'adminInformation' => $adminInfo
        ],200);
    }

    public function adminSignIn(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if(!$admin || !Hash::check($request->password,$admin->password)) {
            throw new Error('Incorrect Email or Password');
        }

        $token = $admin->createToken('admin-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'admin' => $admin,
            'token' => $token
        ],200);
    }

    public function adminSignOut(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Successfully'
        ],200);
    }
}
