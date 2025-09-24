<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Teacher__subject;
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


        try{
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
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'Failed Server Error'
            ],500);
        }

    }

    public function teacherSignIn(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try{
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
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'User not found'
            ],500);
        }
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

            if(!$teacher instanceof Teacher || $teacher->id != $teacherId) {
                return response()->json([
                    'message' , 'Unauthorized. ONly teacher view the subject',
                ],403);
            }

           $subjectIds = Teacher__subject::where('teacher_id', $teacher->id)
                                        ->pluck('subject_id');

            $subjects = Subject::whereIn('id',$subjectIds)->get();

            if($subjects->isEmpty()) {
                return response()->json([
                    'message' => 'No Subject found for this teacher',
                    'subjects' => []
                ],404);
            }

            return response()->json([
            'message' => 'Successfully fetched all Subject handled.',
            'activities' => $subjects
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.'
            ], 404);
        }
    }
}
