<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherInformation extends Model
{
    protected $fillable = [
        'teacher_id',
        'first_name',
        'last_name',
        'address',
        'date_of_birth',
        'subject_specialty'
    ];

    public function teacher(): BelongsTo{
        return $this->belongsTo(Teacher::class);
    }
}
