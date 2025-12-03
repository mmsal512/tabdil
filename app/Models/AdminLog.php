<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Added for the admin relationship

class AdminLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'data_before',
        'data_after',
    ];

    protected $casts = [
        'data_before' => 'array',
        'data_after' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
