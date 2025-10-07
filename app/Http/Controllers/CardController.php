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
    function getAllCards(Request $request){
        $AllCards=Card::all();
        if($AllCards){
        return response()->json([
            'code'=>200,
            'message'=>'Get All Cards Successfully',
            'data'=>[
                'All Cards'=>$AllCards
            ]
            ]);
        }
    }

    function getCard($id){
        $card = Card::find($id);
        if($card){
        return response()->json([
            'code'=>200,
            'message'=>'Get Card Successfully',
            'data'=>[
                'Card'=>$card
            ]
            ]);
        }
    }

    function deleteCard($id){
        $deleteCard=Card::find($id);
        $deleteCard->delete();
        return response()->json([
            'code'=>404,
            'message'=>'Card Not Found'],);
    }

    function updateCard(Request $request,$id){
        $card =Card::find($id);
        if(!$card){
             return response()->json([
             'code'=>404,
             'message'=>'Card Not Found']);
        }
        $CodeCardUpdate = $request->only(['code']);
        $card->update($CodeCardUpdate);
        $card->save();
        return response()->json([
            'card'=>200,
            'message'=>'Card Updated successfully',
            'data'=>[
                'card_id'=>$card->id,
                'new code'=>$card->code,
            ]
        
        ],);
    }

    public function createCardForUser(Request $request,$user_id){
       try{
        $user=User::find($user_id);
        if($user){
            $card = $user->card()->create([
                "code"=>$request->input('code')
            ]);
            return response()->json([
                'card'=>404,
                'message'=>'Card Not Found'],);
        }
       return response()->json([
        'card'=>404,
        'message'=>'failed there no user with this ID'],);
    }catch (UniqueConstraintViolationException $e) { 
            return response()->json([
                'code' => 409, 
                'message' => 'The card code is already in use.'],);
       } 
    }
}
