<?php

namespace App\Repository;
 use App\Models\CardTransaction;

 class CardTransactionRepository{
    function findCardTransactionByCardID($card){

        $cardTransaction=CardTransaction::where('card_id',$card->id)->latest()->first();
        return $cardTransaction;
    }
    function createCardTransaction($card,$type){
        $cardTransaction=CardTransaction::create([
            'card_id'=>$card->id,
            'type'=>$type
        ]);
        return $cardTransaction;
    }
 }