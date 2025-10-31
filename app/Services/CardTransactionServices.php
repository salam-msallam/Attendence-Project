<?php

namespace App\Services;
 use App\Models\CardTransaction;
 use App\Repositories\CardRepository;
 use App\Repository\CardTransactionRepository;
 use App\Exceptions\UserAlreadyExistInClubException;
 use App\Exceptions\ModelNotFoundException;
 use App\Exceptions\UserAlreadyNotExistInClubException;
 use App\Exceptions\CardAttendanceNotFoundException;
use Carbon\Carbon;
use App\Repositories\CardTransactionRepositories;

 class CardTransactionServices{

    protected $cardRepository;
    protected $cardTransactionRepository;

    public function __construct(CardRepository $cardRepository,CardTransactionRepositories $cardTransactionRepository) {
        $this->cardRepository = $cardRepository;
        $this->cardTransactionRepository = $cardTransactionRepository;
    }


    function createCardTransaction($code){
        $card=$this->cardRepository->findCardByCode($code);
        if($card){
            $cardTransaction=$this->cardTransactionRepository->findCardTransactionByCardID($card);
            if($cardTransaction&&$cardTransaction->type=="enter"){
               throw new UserAlreadyExistInClubException();
                }
                $cardTransaction=$this->cardTransactionRepository->createCardTransaction($card,"enter");
                return $cardTransaction;
        }
        throw new ModelNotFoundException();
    }


    function logoutFromClub($user){
        $card=$this->cardRepository->findCardByUserID($user->id);
        if(!$card){
            throw new CardAttendanceNotFoundException();
        }
        $cardTransaction=$this->cardTransactionRepository->findCardTransactionByCardID($card);
        if(!$cardTransaction||$cardTransaction->type=="Exit"){
            throw new UserAlreadyNotExistInClubException();
        }
        $cardTransaction=$this->cardTransactionRepository->createCardTransaction($card,"Exit");
        return $cardTransaction;

    }
 
    public function AttendanceService($user){
        $card = $this->cardRepository->FindCardByUserId($user->id);
        if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        $card_id=$card->id;
        $transaction = $this->cardTransactionRepository->getEntryTransactionsByCardId($card_id);
        $entryRecords = [];
        foreach($transaction as $transaction){
            $entryTime = Carbon::parse($transaction->created_at);
            
            $entryRecords[] = [
                'Login Date' => $entryTime->format('F j, Y'), 
                'Login Time' => $entryTime->format('h:i A'), 
            ];
        }
        return $entryRecords;
    }

    public function getTotalMonthlyAttendanceService($user){
        $card = $this->cardRepository->FindCardByUserId($user->id);
         if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        $card_id=$card->id;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $transactions = $this->cardTransactionRepository->getTransactionsByCardId($card_id,$startOfMonth,$endOfMonth);

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
        $lastEnter = $this->cardTransactionRepository->LastLogin($card_id); 

        $LastLogin=[];
        if ($lastEnter) {
            $transfer = Carbon::parse($lastEnter->created_at);
            $LastLogin[] = [
                    'Login Date' => $transfer->format('F j, Y'), 
                    'Login Time' => $transfer->format('h:i A'), 
            ];
        }

        //Last Logout
        $lastExit = $this->cardTransactionRepository->LastLogout($card_id);

        $LastLogout=[];
        if ($lastExit) {
            $transfer = Carbon::parse($lastExit->created_at);
            $LastLogout[] = [
                    'Logout Date' => $transfer->format('F j, Y'), 
                    'Logout Time' => $transfer->format('h:i A'), 
            ];
        }

        return [
            'roundedHours'=>$roundedHours,
            'LastLogin'=>$LastLogin,
            'LastLogout'=>$LastLogout
        ];
    }

    public function Attendance_Records_By_UserId_Service($user_id){
        $card = $this->cardRepository->FindCardByUserId($user_id);
        if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        
        $CardTranaction = $this->cardTransactionRepository->getEntryTransactionsByCardId($card->id);
        $EntryRecordsForUser = [];
        foreach($CardTranaction as $CardTranaction){
            $transfer = Carbon::parse($CardTranaction->created_at);
            $EntryRecordsForUser[] = [
                    'Login Date' => $transfer->format('F j, Y'), 
                    'Login Time' => $transfer->format('h:i A'), 
            ];
        }

        return $EntryRecordsForUser;

    }
}
