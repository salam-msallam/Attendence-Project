<?php

namespace App\Models;

use App\Models\Card;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HasFactory;


class CardTransaction extends Model
{
    protected $table='card_transactions';

    protected $fillable=[
        'card_id',
        'type'
    ];

    public function cardTranacion():HasMany{
        return $this->hasMany(Card::class);
    }
}
