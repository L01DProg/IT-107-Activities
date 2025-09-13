<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Student_subject;
use App\Models\Teacher;
use App\Models\Teacher__subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
   public function addSubject(Request $request) {
        $validatedData = $request->validate([
            'subject_name' => 'required'
        ]);

        
        $admin = $request->user();

        if(!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only Admin can add subjects'
            ]);
        }

        $subject = $admin->subject()->create([
            'subject_name' => $validatedData['subject_name'],
            'admin_id' => $admin->id
        ]);

        return response()->json([
            'message' => 'Successfully added Subject',
            'subject' => $subject
        ]);
   }

   public function assignSubjectTeacher(Request $request) {
        $validatedData = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $admin = $request->user();

        if(!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only Admin can Assign a Subject to a Teacher'
            ]);
        }

        $subjectOfTeacher = Teacher__subject::create($validatedData);

        return response()->json([
            'message' => 'Successfully Assign to Teacher',
            'teacherSubject' => $subjectOfTeacher 
        ]);
   }

   public function studentSubject(Request $request) {
        $validatedData = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id'
        ]);

        $teacher = $request->user();

        if(!$teacher instanceof Teacher) {
            return response()->json([
                'message' => 'Unauthenticated. Only Teacher can assign subject to students'
            ]);
        }

        $studentSubject = Student_subject::create($validatedData);


        return response()->json([
            'message' => 'Successfully Assign to Student',
            'subject' => $studentSubject
        ]);

   }
}
