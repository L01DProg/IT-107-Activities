<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
