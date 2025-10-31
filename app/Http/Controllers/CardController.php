<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardValidateRequest;
use App\Http\Requests\UpdateCardValidateRequest;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Models\Card;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException; 
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Validator;


class CardController extends Controller
{
    function getAllCards(){
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
        return response()->json([
            'code'=>404,
            'message'=>'Card Not Found'
        ]);
    }

    function deleteCard($id){
        $deleteCard=Card::find($id);
        $deleteCard->delete();
        if($deleteCard){
            return response()->json([
            'code'=>200,
            'message'=>'Deleted Card Successfully'
        ]);
        }
        return response()->json([
            'code'=>404,
            'message'=>'Card Not Found'
        ]);
    }

    function updateCard(UpdateCardValidateRequest $request,$id){
        $card =Card::find($id);
        if(!$card){
             return response()->json([
             'code'=>404,
             'message'=>'Card Not Found'
            ]);
        }
        $CodeCardUpdate = $request->validated();
        $card->update($CodeCardUpdate);
        $card->save();
        return response()->json([
            'code'=>200,
            'message'=>'Card Updated successfully',
            'data'=>[
                'card_id'=>$card->id,
                'new code'=>$card->code,
            ]
        ]);
    }

    public function createCardForUser(CardValidateRequest $request,$user_id){
       try{
        $user=User::find($user_id);
        if($user){
            $card = $user->card()->create($request->validated());
            return response()->json([
                'code'=>201,
                'message'=>'Create Card For This User Successfully'
            ]);
        }
       return response()->json([
        'code'=>404,
        'message'=>'failed there no user with this ID'
    ]);
    }catch (UniqueConstraintViolationException $e) { 
            return response()->json([
                'code' => 409, 
                'message' => 'The card code is already in use.'
            ]);
       }
    }
}
