<?php

namespace App\Http\Controllers;

use App\Models\Activity;
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

        $activity = Activity::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'submission' => $validatedData['submission'],
            'subject_id' => $subject->id
        ]);

        return response()->json([
            'message' => 'Successfully Create Activity',
            'activity' => $activity
        ],200);
    }
}
