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
    
    
    function Attendance_Records_For_User(){
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $card =Card::where('user_id',$user->id)->first();
        if (!$card) {
            return response()->json(['message' => 'No attendance card found for this user.'], 404);
        }

        $card_id=$card->id;
        $transaction = CardTransaction::where('card_id', $card_id)->where('type','enter')->orderBy('created_at','desc')->get();

        $entryRecords = [];
        foreach($transaction as $transaction){
            $entryTime = Carbon::parse($transaction->created_at);
            
            $entryRecords[] = [
                'Login Date' => $entryTime->format('F j, Y'), 
                'Login Time' => $entryTime->format('h:i A'), 
            ];
        }
         return response()->json([
            'user_id' => $user->id,
            'entry_records' => $entryRecords
        ], 200);
    }


    public function getTotalMonthlyAttendance()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $card =Card::where('user_id',$user->id)->first();

        if (!$card) {
            return response()->json(['message' => 'No attendance card found for this user.'], 404);
        }

        $card_id=$card->id;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $transactions = CardTransaction::where('card_id', $card_id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc') 
            ->get();

        $totalDurationInMinutes = 0;
        $entryTime = null;
        foreach ($transactions as $transaction) {
            $currentTime = Carbon::parse($transaction->created_at);

            if ($transaction->type == 'enter') {
                $entryTime = $currentTime;
            } elseif ($transaction->type == 'Exit' && $entryTime !== null) {
                $duration = $entryTime->diffInMinutes($currentTime);
                
                $totalDurationInMinutes += $duration;

                $entryTime = null; 
            }
        }
        $totalDurationInHours = $totalDurationInMinutes / 60;
        
        $roundedHours = (int)ceil($totalDurationInHours);

        //Last Login
        $lastEnter = CardTransaction::where('card_id', $card->id) ->where('type', 'enter')->latest()->first();

        $LastLogin=[];
        if ($lastEnter) {
            $transfer = Carbon::parse($lastEnter->created_at);
            $LastLogin[] = [
                    'Login Date' => $transfer->format('F j, Y'), 
                    'Login Time' => $transfer->format('h:i A'), 
            ];
        }

        //Last Logout
        $lastExit = CardTransaction::where('card_id',$card->id)->where('type','Exit')->latest()->first();

        $LastLogout=[];
        if ($lastExit) {
             $transfer = Carbon::parse($lastExit->created_at);
            $LastLogout[] = [
                    'Logout Date' => $transfer->format('F j, Y'), 
                    'Logout Time' => $transfer->format('h:i A'), 
            ];
        }

        return response()->json([
            'user_id' => $user->id,
            'month' => Carbon::now()->format('F Y'),
            'Total Hours of attendance at the club ' => $roundedHours, 
            'Last Login ' =>$LastLogin,
            'Last Logout ' =>$LastLogout
        ], 200);


    }
}

