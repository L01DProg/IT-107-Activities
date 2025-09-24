<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Student_subject;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Teacher__subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function addSubject(Request $request)
    {
        $validatedData = $request->validate([
            'subject_name' => 'required',
            'description' => 'required'
        ]);

        try{
            
            $admin = $request->user();

            if (!$admin instanceof Admin) {
                return response()->json([
                    'message' => 'Unauthenticated. Only Admin can add subjects'
                ],401);
            }
            
           
            $checkSubject = Subject::where('subject_name',$request->subject_name)->first();

            if($checkSubject) {
                return response()->json([
                    'message' => 'Subject Already Exists'
                ],500);
            }

            $subject = $admin->subject()->create([
                'subject_name' => $validatedData['subject_name'],
                'description' => $validatedData['description'],
                'admin_id' => $admin->id
            ]);

            return response()->json([
                'message' => 'Successfully added Subject',
                'subject' => $subject
            ],200);
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'Failed to create subject'
            ],500);
        }
    }

    public function assignSubjectTeacher(Request $request)
    {
        $validatedData = $request->validate([
            'teacher_id' => 'required',
            'subject_id' => 'required'
        ]);

       try{
        
            $admin = $request->user();

            if (!$admin instanceof Admin) {
                return response()->json([
                    'message' => 'Unauthenticated. Only Admin can Assign a Subject to a Teacher'
                ],401);
            }

            $checkSubject = Teacher__subject::where('subject_id', $request->subject_id)
                                            ->where('teacher_id', $request->teacher_id)
                                            ->get();

            if (count($checkSubject)) {
                return response()->json([
                    'message' => 'Already Exist subject'
                ], 500);
            }

            $subjectOfTeacher = Teacher__subject::create($validatedData);

            return response()->json([
                'message' => 'Successfully Assign to Teacher',
                'teacherSubject' => $subjectOfTeacher
            ]);
       }catch(\Exception $e) {
        return response()->json([
            'message' => 'Failed Server Error'
        ],500);
       }
    }

    public function studentSubject(Request $request)
    {
        $validatedData = $request->validate([
            'subject_id' => 'required',
            'student_id' => 'required'
        ]);

        try{
            $teacher = $request->user();

            if (!$teacher instanceof Teacher) {
                return response()->json([
                    'message' => 'Unauthorized. Only Teacher can assign subject to students'
                ],403);
            }

            $checkSubject = Student_subject::where('subject_id', $request->subject_id)
                                            ->where('student_id', $request->student_id)
                                            ->get();

            if (count($checkSubject)) {
                return response()->json([
                    'message' => 'Already Exist subject'
                ], 500);
            }

            $studentSubject = Student_subject::create($validatedData);


            return response()->json([
                'message' => 'Successfully Assign to Student',
                'subject' => $studentSubject
            ],200);
        }catch(\Exception $e) {
            return response()->json([
                'message' => 'Failed Server Error'
            ],500);
        }
    }
}
