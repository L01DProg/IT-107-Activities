<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student_Activity extends Model
{
    protected $fillable = [
        'student_id',
        'activity_id'
    ];
}
