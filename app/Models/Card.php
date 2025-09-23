<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\cardTranacion;
use HasFactory;
class Card extends Model
{
    protected $table='Cards';
    protected $fillable=[
        'code'
    ];
    
   public function user():BelongTo{
    return $this->belongTo(User::class,'user_id');
   }
   
   public function cardTranacion():BelongTo{
    return $this->belongTo(cardTranacion::class);
}
}
