<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function teacherRegister(Request $request) {
        $validatedData = $request->validate([
            'fullname' => 'required|string',
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        
    }
}
