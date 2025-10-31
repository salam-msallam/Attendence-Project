<?php

namespace App\Repositories;

use App\Models\Card;


class CardRepositories{
    public function FindCardByUserId($user_id){
        $card =Card::where('user_id',$user_id)->first();
        return $card;
    }
    
}