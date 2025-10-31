<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardServices;

class CardController extends Controller
{
    protected $cardServices;
    public function __construct(CardServices $cardServices){
        $this->cardServices=$cardServices;
    }

    function getAllCards(Request $request){

        $response=$this->cardServices->getAllCards();
            return response()->json([
                'code'=>200,
                'message'=>'Get All Cards Successfully',
                'data'=>[
                    'All Cards'=>$response
                ]
        ]); 
    }

    function getCard($id){

        $response = $this->cardServices->getCard($id);
            return response()->json([
                'code'=>200,
                'message'=>'Get Card Successfully',
                'data'=>[
                    'Card'=>$response
                ]
        ]);  
    }

    function deleteCard($id){
        $response=$this->cardServices->deleteCard($id);  
        if($response){
            $response->delete();
             return response()->json([
            'code'=>200,
            'message'=>'Deleted Card Successfully'
             ]);
        }
       
    }

    public function updateCard(Request $request, int $id)
    {
        $dataToUpdate = $request->only(['code']);
        $card = $this->cardServices->updateCard($id, $dataToUpdate);
        return response()->json([
            'code' => 200,
            'message' => 'Card Updated successfully',
            'data' => [
                'card_id' => $card->id,
                'new_code' => $card->code,
            ]
        ]);
    }
    function createCardForUser(Request $request,$user_id){
    
    $data=$request->all();
    $response=$this->cardServices->createCardForUser($user_id,$data);
    return response()->json([
        'code'=>200,
        'message'=>'Create card for this user successfully']);
    }
}
