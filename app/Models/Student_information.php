<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student_information extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'student_id'
    ];
}
