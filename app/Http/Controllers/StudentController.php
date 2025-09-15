<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Student_Activity;
use App\Models\Student_information;
use Error;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function studentRegister(Request $request, Student $students, Student_information $studentInformations)
    {
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

        if (!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only admin can Register the Students'
            ],401);
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

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
        ],200);
    }

    public function studentSignIn(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $student = Student::where('username', $request->username)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            throw new Error("Incorrect Username or Password");
        }

        $token = $student->createToken('student-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'token' => $token,
            'student' => $student
        ],200);
    }


    public function studentSignOut(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Successfully'
        ],200);
    }

    public function viewSubject(Request $request, $studentId)
    {
        try {

            $student = $request->user();

            if(!$student instanceof Student) {
                return response()->json([
                    'message' , 'Unauthenticated. ONly Student view the subject',
                ],401);
            }

            $students = Student::findOrFail($studentId);

            $studentSubjects = $students->with(['subjects'])->get();

            return response()->json([
                'message' => 'Successfully fetched subjects for student.',
                'subjects' => $studentSubjects
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.'
            ], 404);
        }
    }

    public function viewActivities(Request $request, $studentId)
    {
    

        $authenticatedStudent = $request->user();

        if (!$authenticatedStudent instanceof Student || $authenticatedStudent->id != $studentId) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        
       
        $activityIds = Student_Activity::where('student_id', $authenticatedStudent->id)
                                    ->pluck('activity_id'); 

       
        $activities = Activity::whereIn('id', $activityIds)->get();

        if ($activities->isEmpty()) {
            return response()->json([
                'message' => 'No activities found for this student.',
                'activities' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Successfully fetched all activities.',
            'activities' => $activities
        ]);
    }
}

