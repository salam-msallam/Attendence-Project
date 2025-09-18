<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CardModel;
use HasFactory;
class userModel extends Model
{
    // use HasFactory, Notifiable;
    protected $table = 'new_users';

    protected $fillable = [
        'name',
        'email',
        'number',
        'password',
    ];

    public function card():HasOne{
        return $this->hasOne(CardModel::class);
    }
}
