<?php

namespace App\Http\Controllers;
use App\Models\Card;
use Illuminate\Http\Request;
use App\Models\User;

class CardController extends Controller
{
    function createCard(Request $request){
        $card=Card::create([
            "code"=>$request->input("code")
        ]);
        return $card;
    }
    function getAllCards(Request $request){
        $AllCards=Card::all();
        return $AllCards;
    }

    function getCard($id){
        $card = Card::find($id);
        return $card;
    }

    function deleteCard($id){
        $deleteCard=Card::find($id);
        $deleteCard->delete();
        return "Card deleted";
    }

    function updateCard($id){
        $card =Card::find($id);
        $card->code = "567-456";
        $card->save();
        return $card;
    }

    public function createCardForUser($user_id){
        $user=User::find($user_id);
        if($user){
            $card = $user->card()->create([
                "code"=>$request->input("code")
            ]);
            return "card Created successfully";
        }
        throw Exception("f;lkj");
    }
}
