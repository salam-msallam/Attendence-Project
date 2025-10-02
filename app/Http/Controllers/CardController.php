<?php

namespace App\Http\Controllers;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Models\Card;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException; 
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Validator;


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

    public function createCardForUser(Request $request,$user_id){
       try{
        $user=User::find($user_id);
        if($user){
            $card = $user->card()->create([
                "code"=>$request->input('code')
            ]);
            return "card Created successfully";
        }
       return "failed there no user with this ID";
    }catch (UniqueConstraintViolationException $e) { 
            return response()->json([
                'code' => 409, 
                'message' => 'The card code is already in use.',
            ], 409);
       } 
    }
}
