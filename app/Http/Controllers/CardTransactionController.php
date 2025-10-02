<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;
use Carbon\Carbon;



class CardTransactionController extends Controller
{
    function CreateCardTransaction($code){
        //Validate & Get card by code
        $card=Card::where('code',$code)->first();

        // Create cardTransacation 
        if($card){
                    $cardTranaction=CardTransaction::create([
                        'card_id'=>$card->id,
                        'type'=>"enter"
                    ]);
                
            return response()->json(["status"=>"Successfully"],201);
        }
        return  response()->json(["status"=>"Not Found"],404);
        
    }
    function logoutFromclub(){
        $user = auth()->user();
        if(!$user){
            return response()->json(['message'=>'Unauthenticated'],401);
        }
        $card=Card::where('user_id',$user->id)->first();
        if(!$card){
            return response()->json(['message'=>'No attendanc card found for this user'],404);
        }
        $cardTranaction=CardTransaction::where('card_id',$card->id)->latest()->first();
        if(!$cardTranaction||$cardTranaction->type=="Exit"){
            return response()->json(['message'=>'you are logout already'],404);
        }
        $cardTranaction=CardTransaction::create([
            'card_id'=>$card->id,
            'type'=>"Exit"
        ]);
            

        
    //    $this->CreateCardTransaction($card->code);
    }
    
    
    function GetDateOfAttendance(){
        $getAllUserTransaction = CardTransaction::all();

        $attendanceList = $getAllUserTransaction->map(
            function($transaction){
                $syriaTime = Carbon::parse($transaction->created_at);
                return [
                    'Login Date' => $syriaTime->format('F j, Y'),
                    'Login Time' => $syriaTime->format('h:i A'),  
                ];
            });
        return response()->json([
            'attendance_records' => $attendanceList
        ]);
    }

    function GetUserProfile(){
        
    }

    
}

