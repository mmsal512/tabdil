<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'base_currency',
        'target_currency',
        'amount',
        'converted_amount',
        'label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
