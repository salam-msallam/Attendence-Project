<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class unknownCard extends Model
{
    protected $table = 'unknown_cards';

    protected $fillable = [
        'code',
    ];
}
