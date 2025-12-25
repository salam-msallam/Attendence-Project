<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\cardTranacion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CardTransaction;
use HasFactory;

class Card extends Model
{
    protected $table = 'Cards';
    protected $fillable = [
        'code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongTo(User::class, 'user_id');
    }

    public function cardTranacion()
    {
        return $this->hasMany(CardTransaction::class);
    }
}
