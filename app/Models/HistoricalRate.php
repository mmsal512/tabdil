<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalRate extends Model
{
    protected $fillable = [
        'date',
        'base_currency',
        'target_currency',
        'rate_value',
    ];

    //
}
