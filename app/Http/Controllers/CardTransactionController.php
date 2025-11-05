<?php

namespace App\Http\Controllers;

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
                'message'=>'The club login process was completed successfully.'],201);
        
    }

    function logoutFromClub(){
        $user = auth()->user();
        $response=$this->cardTransactionServices->logoutFromClub($user);
        return response()->json([
            'code'=>200,
            'message'=>'The club logout process was completed successfully.'],200);
       
    }
    
    
    function Attendance_Records_For_User(){
        $user = auth()->user();
        $entryRecords = $this->cardTransactionServices->AttendanceService($user);
         return response()->json([
            'code'=>200,
            'message'=>"Successfully retrieved this user's entry records" ,
            'data'=>[
                'user_id' => $user->id,
                'entry_records' => $entryRecords
            ],
        ],200); 
    }


    public function getTotalMonthlyAttendance()
    {
        $user = auth()->user();
        $value = $this->cardTransactionServices->getTotalMonthlyAttendanceService($user);
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
        ],200);
    }

    function Attendance_Records_By_UserId($user_id){
        $EntryRecordsForUser = $this->cardTransactionServices->Attendance_Records_By_UserId_Service($user_id);
        return response()->json([
            'code'=> 200,
            'message'=>"This user's login records were successfully fetched ",
            'data'=>[
                'user_id' =>$user_id,
                'Entry records For this user ' => $EntryRecordsForUser
            ]
        ],200);
    }
}

