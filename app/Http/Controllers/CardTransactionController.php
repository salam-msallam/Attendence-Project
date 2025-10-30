<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;
use App\Services\CardTransactionServices;
use Carbon\Carbon;



class CardTransactionController extends Controller
{
    protected $cardTransactionServices;
    public function __construct(CardTransactionServices $cardTransactionServices){
        $this->cardTransactionServices=$cardTransactionServices;
    }
    function createCardTransaction($code){

        $response=$this->cardTransactionServices->createCardTransaction($code);
        return response()->json([
                'code'=>201,
                'message'=>'The club login process was completed successfully.'],);
        
    }
    function logoutFromClub(){
        $user = auth()->user();
        $response=$this->cardTransactionServices->logoutFromClub($user);
        return response()->json([
            'code'=>201,
            'message'=>'The club logout process was completed successfully.'],);
       
    }
    
    
    function Attendance_Records_For_User(){
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'code'=>401,
                'message' => 'Unauthenticated'],);
        }

        $card =Card::where('user_id',$user->id)->first();
        if (!$card) {
            return response()->json([
                'code'=>404,
                'message' => 'No attendance card found for this user.'],);
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
            'code'=>200,
            'message'=>"Successfully retrieved this user's entry records" ,
            'data'=>[
                'user_id' => $user->id,
                'entry_records' => $entryRecords
            ],
        ]); 
    }


    public function getTotalMonthlyAttendance()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'code'=>401,
                'message' => 'Unauthenticated'], );
        }

        $card =Card::where('user_id',$user->id)->first();

        if (!$card) {
            return response()->json([
                'code'=>404,
                'message' => 'No attendance card found for this user.'], );
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

        return  response()->json([
            'code'=>200,
            'message'=>"The number of attendance hours, last login and last logout have been successfully fetched",
            'data'=>[
            'user_id' => $user->id,
            'month' => Carbon::now()->format('F Y'),
            'Total Hours of attendance at the club ' => $roundedHours, 
            'Last Login ' =>$LastLogin,
            'Last Logout ' =>$LastLogout
            ],
        ]);


    }
}

