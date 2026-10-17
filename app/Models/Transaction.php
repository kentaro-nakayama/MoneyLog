<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'category',
        'date',
        'memo',
    ];

    // TODO: casts()の追加

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
