<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'submission',
        'subject_id'
    ];

    public function subjects(): BelongsTo{
        return $this->belongsTo(Subject::class);
    }

    public function student(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_activity', 'activity_id', 'student_id');
    }
}
