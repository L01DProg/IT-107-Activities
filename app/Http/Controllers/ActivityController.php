<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Student_Activity;
use App\Models\Student_subject;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function activityOfStudent(Request $request,$subjectId) {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'submission' => 'nullable|date',
        ]);

        $teacher = $request->user();

        if(!$teacher instanceof Teacher) {
            return response()->json([
                'message' => 'Unauthenticated. Only Teacher can create Activity',
            ],401);
        }
        
        $subject = Subject::findOrFail($subjectId);

        $checkSubject = Student_subject::where('subject_id',$subject->id)->first();

        $activity = Activity::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'submission' => $validatedData['submission'],
            'subject_id' => $subject->id
        ]);

        $studentActivity = Student_Activity::create([
            'student_id' => $checkSubject->student_id,
            'activity_id' => $activity->id
        ]);

        return response()->json([
            'message' => 'Successfully Create Activity',
            'activity' => $activity
        ],200);
    }
}
