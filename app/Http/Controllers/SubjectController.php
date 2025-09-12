<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
   public function assignSubject(Request $request) {
        $validatedData = $request->validate([
            'subject_name'
        ]);

        
        $admin = $request->user();

        if(!$admin instanceof Admin) {
            return response()->json([
                'message' => 'Unauthenticated. Only Admin can add subjects'
            ]);
        }

        $subject = $admin->subject()->create($validatedData);

        return response()->json([
            'message' => 'Successfully added Subject',
            'subject' => $subject
        ]);
   }
}
