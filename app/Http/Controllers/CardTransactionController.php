<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;
use Carbon\Carbon;



class CardTransactionController extends Controller
{
    // function SetTypeTransaction($id){
    //     $cardTranaction=CardTransaction::where('card_id',$id)->last();

    //     if($cardTranaction){
    //         if($cardTranaction->type=="Exit"){
    //             $cardTranaction->type="enter";
    //         }
    //         else if($cardTranaction->type=="enter"){
    //             $cardTranaction->type="Exit";
    //         }
        

    // }
    function CreateCardTransaction($code){
        //Validate & Get card by code
        $card=Card::where('code',$code)->first();

        // Create cardTransacation 
        if($card){
            $cardTranaction=CardTransaction::where('card_id',$card->id)->latest()->first();
            $newType="enter";
            if($cardTranaction){
                if($cardTranaction->type=="Exit"){
                    $newType="enter";
                }
                else $newType="Exit";
            }

                    $cardTranaction=CardTransaction::create([
                        'card_id'=>$card->id,
                        'type'=>$newType
                    ]);
                
            
            // -> type of this cardTransaction is enter
          
                
            return response()->json(["status"=>"Successfully"],201);
        }
        return  response()->json(["status"=>"Not Found"],404);
        
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

    // function FindCardByCode($code){
    //     $card=Card::find($code);
    //     $IsFind=false;
    //     if($card){
    //         $IsFind=True;
    //     }
    //     return $IsFind;
    // }
}

