<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\CardTransaction;
use App\Exceptions\RouteNotFoundException;
use App\Services\CardTransactionServices;
use Carbon\Carbon;



class CardTransactionController extends Controller
{

     protected $CardTransService;
     
    public function __construct(CardTransactionServices $CardTransService){
        $this->CardTransService = $CardTransService;
    }


    function CreateCardTransaction($code){
        //Validate & Get card by code
        $card=Card::where('code',$code)->first();

        // Create cardTransacation 
        if($card){
                    $cardTranaction=CardTransaction::create([
                        'card_id'=>$card->id,
                        'type'=>"enter"
                    ]);
                
            return response()->json([
            'code'=>201,
            'message'=>'The club login process was completed successfully.'
        ]);
        }
        return  response()->json([
            'code'=>404,
            'message'=>'Not Found'
        ]);
        
    }
    function logoutFromclub(){
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'code'=>401,
                'message'=>'Unauthenticated'
            ]);
        }
        $card=Card::where('user_id',$user->id)->first();
        if(!$card){
            return response()->json([
                'code'=>404,
                'message'=>'No attendance card found for this user'
            ]);
        }
        $cardTranaction=CardTransaction::where('card_id',$card->id)->latest()->first();
        if(!$cardTranaction||$cardTranaction->type=="Exit"){
            return response()->json([
                'code'=>404,
                'message'=>'you are logout already'
            ]);
        }
        $cardTranaction=CardTransaction::create([
            'card_id'=>$card->id,
            'type'=>"Exit"
        ]);
        return response()->json([
            'code'=>201,
            'message'=>'The club logout process was completed successfully.'
        ]);
    }
    
    
    function Attendance_Records_For_User(){
        $user = auth()->user();
        $entryRecords = $this->CardTransService->AttendanceService($user);
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
        $value = $this->CardTransService->getTotalMonthlyAttendanceService($user);
        return  response()->json([
            'code'=>200,
            'message'=>"The number of attendance hours, last login and last logout have been successfully fetched",
            'data'=>[
            'user_id' => $user->id,
            'month' => Carbon::now()->format('F Y'),
            'Total Hours of attendance at the club ' => $value ['roundedHours'], 
            'Last Login ' =>$value ['LastLogin'],
            'Last Logout ' =>$value ['LastLogout']
            ],
        ]);
    }

    function Attendance_Records_By_UserId($user_id){
        $EntryRecordsForUser = $this->CardTransService->Attendance_Records_By_UserId_Service($user_id);
        return response()->json([
            'code'=> 200,
            'message'=>"This user's login records were successfully fetched ",
            'data'=>[
                'user_id' =>$user_id,
                'Entry records For this user ' => $EntryRecordsForUser
            ]
        ]);
    }
}

