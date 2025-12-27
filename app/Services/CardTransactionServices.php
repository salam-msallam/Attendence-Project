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
use App\Http\Controllers\unknownCardController;

class CardTransactionServices
{

    protected $cardRepository;
    protected $cardTransactionRepository;
    protected $unknownCardController;

    public function __construct(CardRepository $cardRepository, CardTransactionRepositories $cardTransactionRepository, unknownCardController $unknownCardController)
    {
        $this->cardRepository = $cardRepository;
        $this->cardTransactionRepository = $cardTransactionRepository;
        $this->unknownCardController = $unknownCardController;
    }


    function createCardTransaction($code)
    {
        $card = $this->cardRepository->findCardByCode($code);
        if ($card) {
            $cardTransaction = $this->cardTransactionRepository->findCardTransactionByCardID($card);
            if ($cardTransaction && $cardTransaction->type == "enter") {
                throw new UserAlreadyExistInClubException();
            }
            $cardTransaction = $this->cardTransactionRepository->createCardTransaction($card, "enter");
            return $cardTransaction;
        }
        $this->unknownCardController->processScan($code);
        throw new ModelNotFoundException();
    }


    function logoutFromClub($user)
    {
        $card = $this->cardRepository->findCardByUserID($user->id);
        if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        $cardTransaction = $this->cardTransactionRepository->findCardTransactionByCardID($card);
        if (!$cardTransaction || $cardTransaction->type == "Exit") {
            throw new UserAlreadyNotExistInClubException();
        }
        $cardTransaction = $this->cardTransactionRepository->createCardTransaction($card, "Exit");
        return $cardTransaction;
    }

    public function AttendanceService($user)
    {
        $card = $this->cardRepository->FindCardByUserId($user->id);
        if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        $card_id = $card->id;
        $transaction = $this->cardTransactionRepository->getEntryTransactionsByCardId($card_id);
        $entryRecords = [];
        foreach ($transaction as $transaction) {
            $entryTime = Carbon::parse($transaction->created_at)->timezone('Asia/Damascus');

            $entryRecords[] = [
                'Login Date' => $entryTime->format('F j, Y'),
                'Login Time' => $entryTime->format('h:i A'),
            ];
        }
        return $entryRecords;
    }

    public function getTotalMonthlyAttendanceService($user)
    {
        $card = $this->cardRepository->FindCardByUserId($user->id);
        if (!$card) {
            throw new CardAttendanceNotFoundException();
        }
        $card_id = $card->id;

        $startOfMonth = Carbon::now('Asia/Damascus')->startOfMonth();
        $endOfMonth = Carbon::now('Asia/Damascus')->endOfMonth();

        $transactions = $this->cardTransactionRepository->getMonthlyTransactionsByCardId($card_id, $startOfMonth, $endOfMonth);

        $totalDurationInMinutes = 0;
        $entryTime = null;
        foreach ($transactions as $transaction) {
            $currentTime = Carbon::parse($transaction->created_at)->timezone('Asia/Damascus');

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

        $LastLogin = [];
        if ($lastEnter) {
            $transfer = Carbon::parse($lastEnter->created_at)->timezone('Asia/Damascus');
            $LastLogin[] = [
                'Login Date' => $transfer->format('F j, Y'),
                'Login Time' => $transfer->format('h:i A'),
            ];
        }

        //Last Logout
        $lastExit = $this->cardTransactionRepository->LastLogout($card_id);

        $LastLogout = [];
        if ($lastExit) {
            $transfer = Carbon::parse($lastExit->created_at)->timezone('Asia/Damascus');
            $LastLogout[] = [
                'Logout Date' => $transfer->format('F j, Y'),
                'Logout Time' => $transfer->format('h:i A'),
            ];
        }

        return [
            'roundedHours' => $roundedHours,
            'LastLogin' => $LastLogin,
            'LastLogout' => $LastLogout
        ];
    }

    public function Attendance_Records_By_UserId_Service($user_id)
    {
        $card = $this->cardRepository->FindCardByUserId($user_id);
        if (!$card) {

            throw new CardAttendanceNotFoundException();
        }

        $CardTransaction = $this->cardTransactionRepository->getTransactionsByCardId($card->id);
        $EntryRecordsForUser = [];
        foreach ($CardTransaction as $transaction) {
            $transactionTime = Carbon::parse($transaction->created_at)->timezone('Asia/Damascus');
            $data = [
                'type' => $transaction->type,
            ];
            if ($transaction->type === 'Exit') {
                $data['logout_date'] = $transactionTime->format('F j, Y');
                $data['logout_time'] = $transactionTime->format('h:i A');
            } else {
                $data['login_date']  = $transactionTime->format('F j, Y');
                $data['login_time']  = $transactionTime->format('h:i A');
            }
            $EntryRecordsForUser[] = $data;
        }

        return $EntryRecordsForUser;
    }
    function findCardTransactionByCardID($card)
    {
        return $this->cardTransactionRepository->findCardTransactionByCardID($card);
    }
     public function getAllUsersAttendancePaginatedService($perPage)
    {
        $transactions = $this->cardTransactionRepository->getAllTransactionsPaginated($perPage);
        $transactions->getCollection()->transform(function ($transaction) {
            $transactionTime = Carbon::parse($transaction->created_at)->timezone('Asia/Damascus');
            $data = [
                'user_id'    => $transaction->user_id,
                'user_name'  => $transaction->first_name . ' ' . $transaction->last_name,
                'type'       => $transaction->type,
            ];
            if ($transaction->type === 'ُExit') {
                $data['logout_date'] = $transactionTime->format('F j, Y');
                $data['logout_time'] = $transactionTime->format('h:i A');
            } else {
                $data['login_date']  = $transactionTime->format('F j, Y');
                $data['login_time']  = $transactionTime->format('h:i A');
            }

            return $data;
        });
        return $transactions;
    }
}
