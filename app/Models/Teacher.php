<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Model
{
    use HasUuids, HasApiTokens;
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
    ];

    public function subjects(): BelongsToMany {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }
}
