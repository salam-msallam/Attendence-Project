<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;



class cardTrasactionController extends Controller
{
    function createCardTransaction(Request $request ,$code){
        $card=Card::where('code',$code)->first();
        if($card){
            $cardTranaction=CardTransaction::create([
                'card_id'=>$card->id
            ]);
            return "Create Card Transaction Successfully";
        }
        return "Faild";
        
    }
    
    // function FindCardByCode($code){
    //     $card=Card::find($code);
    //     $IsFind=false;
    //     if($card){
    //         $IsFind=True;
    //     }
    //     return $IsFind;
    // }
}
