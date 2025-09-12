<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher__subject extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id'
    ];
}
