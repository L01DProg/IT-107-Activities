<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\TeacherInformation;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function teacherRegister(Request $request,Teacher $teachers,TeacherInformation $teacherInformation)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'subject_specialty' => 'nullable|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);


        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only Admin can Assign a Subject to a Teacher'
            ],401);
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $teacher = $teachers->create($validatedData);

        $teacherInfo = $teacherInformation->create([
            'teacher_id' => $teacher->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'subject_specialty' => $request->subject_specialty
        ]);

        return response()->json([
            'message' => 'Teacher Created Successfully',
            'teacher' => $teacher,
            'teacher_information' => $teacherInfo
        ],200);

    }

    public function teacherSignIn(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $teacher = Teacher::where('email',$request->email)->first();

        if(!$teacher || !Hash::check($request->password,$teacher->password)) {
            throw new Error("Incorrect Email or Password");
        }

        $token = $teacher->createToken('teacher-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'teacher' => $teacher,
            'token' => $token
        ],200);
    }


    public function teacherSignOut(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Successfully'
        ],200);
    }

    public function viewSubject(Request $request,$teacherId)
    {
        try {

            $teacher = $request->user();

            if(!$teacher instanceof Teacher) {
                return response()->json([
                    'message' , 'Unauthenticated. ONly Student view the subject',
                ],401);
            }

            $teachers = Teacher::findOrFail($teacherId);

            $teacherSubjects = $teachers->with(['subjects'])->get();

            return response()->json([
                'message' => 'Successfully fetched subjects for student.',
                'subjects' => $teacherSubjects
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.'
            ], 404);
        }
    }
}
