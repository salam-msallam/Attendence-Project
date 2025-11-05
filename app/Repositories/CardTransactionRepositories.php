<?php

namespace App\Repositories;
use App\Models\CardTransaction;

class CardTransactionRepositories{
   public function getEntryTransactionsByCardId($card_id){
        return CardTransaction::where('card_id', $card_id)->where('type','enter')->orderBy('created_at','desc')->get();
   }

   public function getTransactionsByCardId($card_id, $startOfMonth,$endOfMonth){
     return CardTransaction::where('card_id', $card_id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc') 
            ->get();
   }

   public function LastLogin($card_id){
    return CardTransaction::where('card_id', $card_id) ->where('type', 'enter')->latest()->first();
   }

    public function LastLogout($card_id){
    return  CardTransaction::where('card_id',$card_id)->where('type','Exit')->latest()->first();
   }

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