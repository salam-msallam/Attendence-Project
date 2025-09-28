<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;
use Carbon\Carbon;



class CardTransactionController extends Controller
{
    function CreateCardTransaction($code){
        $card=Card::where('code',$code)->first();
        if($card){
            $cardTranaction=CardTransaction::create([
                'card_id'=>$card->id
            ]);
            return response()->json(["status"=>"Successfully"]);
        }
        return  response()->json(["status"=>"Failed"]);
        
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
