<?php

namespace App\Http\Controllers;
use App\Models\Card;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Configuration\Exceptions;

class CardController extends Controller
{
    // function createCard(Request $request){
    //     $card=Card::create([
    //         "code"=>$request->input("code")
    //     ]);
    //     return $card;
    // }

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

    function updateCard(Request $request,$id){
        $card =Card::find($id);
        if(!$card){
             return response()->json(['message'=>'Card Not Found'],404);
        }
        $CodeCardUpdate = $request->only(['code']);
        $card->update($CodeCardUpdate);
        $card->save();
        return response()->json(['message'=>'Card  Updated successfully'],200);
    }

    public function createCardForUser(Request $request,$user_id){
        $user=User::find($user_id);
        if($user){
            $card = $user->card()->create([
                "code"=>$request->input('code')
            ]);
            return "card Created successfully";
        }
       return "failed";
    }
}
