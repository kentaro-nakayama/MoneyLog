<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

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
