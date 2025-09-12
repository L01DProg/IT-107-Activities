<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens;
    protected $fillable = [
        'username',
        'email',
        'password'
    ];

    public function adminInformation(): HasOne{
        return $this->hasOne(Admin_information::class);
    }

    public function subject():HasMany{
        return $this->hasMany(Subject::class);
    }
}
