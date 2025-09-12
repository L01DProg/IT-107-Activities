<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Model
{
    use HasUuids, HasApiTokens;
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    public function subjects(): BelongsToMany {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }

    public function teacherInformation(): HasOne{
        return $this->hasOne(TeacherInformation::class);
    }
}
