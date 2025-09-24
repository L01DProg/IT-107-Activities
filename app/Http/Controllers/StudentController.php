<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Student_Activity;
use App\Models\Student_information;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
   
    public function studentRegister(Request $request)
    {
        
        $user = $request->user();
        if (!$user instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only admins can register students.'
            ], 401);
        }

        
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'address' => 'required|string|max:255',
                'profile' => 'nullable|image|mimes:png,jpeg,gif,svg,webp|max:2048', 
                'username' => 'required|string|max:255|unique:students,username',
                'email' => 'required|email|max:255|unique:students,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        
        DB::beginTransaction();
        try {
            
            $imagePath = null;
            if ($request->hasFile('profile')) {
                $imagePath = $request->file('profile')->store('profiles', 'public');
            }

           
            $student = Student::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'profile' => $imagePath,
            ]);

            
            $studentInformation = $student->information()->create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'address' => $validatedData['address'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Successfully created student account.',
                'student' => $student,
                'student_information' => $studentInformation,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create student account.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            'Token' => $token,
            'User' => $student
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

