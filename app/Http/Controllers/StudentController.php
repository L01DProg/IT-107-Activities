<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Student;
use App\Models\Student_information;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function studentRegister(Request $request, Student $students, Student_information $studentInformations) {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = $request->user();

        if(!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only admin can Register the Students'
            ]);
        }

        $student = $students->create($validatedData);

        $studentInformation = $studentInformations->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'student_id' => $student->id
        ]);


        return response()->json([
            'message' => 'Successfully created Student Account',
            'student' => $student,
            'student_information' => $studentInformation
        ]);
    }

    public function studentSignIn(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $student = Student::where('username', $request->username)->first();

        if(!$student || !Hash::check($request->password, $student->password)){
            throw new Error("Incorrect Username or Password");
        }

        $token = $student->createToken('student-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'token' => $token,
            'student' => $student
        ]);
    }


    public function studentSignOut(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Successfully'
        ]);
    }
}
