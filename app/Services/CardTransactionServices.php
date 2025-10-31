<?php

namespace App\Services;
use App\Exceptions\UnauthenticatedException;
use App\Exceptions\CardNotFoundException;
use App\Repositories\CardRepositories;
use Carbon\Carbon;
use App\Repositories\CardTransactionRepositories;

class CardTransactionServices{
    protected $CardRepository;
    protected $CardTransRepository;

     public function __construct(CardRepositories $cardRepository, CardTransactionRepositories $cardTransRepository) {
        $this->CardRepository = $cardRepository;
        $this->CardTransRepository = $cardTransRepository;
    }


    public function AttendanceService($user){
        $card = $this->CardRepository->FindCardByUserId($user->id);
        if (!$card) {
            throw new CardNotFoundException();
        }
        $card_id=$card->id;
        $transaction = $this->CardTransRepository->getEntryTransactionsByCardId($card_id);
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
        $card = $this->CardRepository->FindCardByUserId($user->id);
         if (!$card) {
            throw new CardNotFoundException();
        }
        $card_id=$card->id;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $transactions = $this->CardTransRepository->getTransactionsByCardId($card_id,$startOfMonth,$endOfMonth);

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
        $lastEnter = $this->CardTransRepository->LastLogin($card_id); 

        $LastLogin=[];
        if ($lastEnter) {
            $transfer = Carbon::parse($lastEnter->created_at);
            $LastLogin[] = [
                    'Login Date' => $transfer->format('F j, Y'), 
                    'Login Time' => $transfer->format('h:i A'), 
            ];
        }

        //Last Logout
        $lastExit = $this->CardTransRepository->LastLogout($card_id);

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
        $card = $this->CardRepository->FindCardByUserId($user_id);
        if (!$card) {
            throw new CardNotFoundException();
        }
        
        $CardTranaction = $this->CardTransRepository->getEntryTransactionsByCardId($card->id);
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