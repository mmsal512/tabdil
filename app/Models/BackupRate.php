<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupRate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'backup_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency',
        'buy_rate',
        'sell_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'buy_rate' => 'decimal:2',
        'sell_rate' => 'decimal:2',
    ];
}
