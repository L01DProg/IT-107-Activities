<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin_information extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'admin_id'
    ];

    public function admin(): BelongsTo {
        return $this->belongsTo(Admin::class);
    }
}
