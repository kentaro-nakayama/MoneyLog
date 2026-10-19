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

    /**
     * 属性の型変換の設定。
     *
     * date を Carbon にしておくと、日付の計算や書式変換をそのまま書ける。
     * amount は、フォームから文字列で送られてきても整数として扱えるようにする。
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
