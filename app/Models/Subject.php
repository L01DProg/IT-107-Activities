<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_name'
    ];

    public function student(): BelongsToMany {
        return $this->belongsToMany(Student::class);
    }

    public function teacher():BelongsToMany {
        return $this->belongsToMany(Teacher::class);
    }

    public function activities(): HasMany {
        return $this->hasMany(Activity::class);
    }

    public function admin(): BelongsTo {
        return $this->belongsTo(Admin::class);
    }
}
