<?php

namespace App\Services;
 use App\Models\CardTransaction;
 use App\Repository\CardRepository;
 use App\Repository\CardTransactionRepository;
 use App\Exceptions\UserAlreadyExistInClubException;
 use App\Exceptions\ModelNotFoundException;
 use App\Exceptions\UnauthenticatedException;
 use App\Exceptions\CardTransactionNotFoundException;
 use App\Exceptions\UserAlreadyNotExistInClubException;

 class CardTransactionServices{

    protected $cardRepository;
    protected $cardTransactionRepository;

    public function __construct(
        CardRepository $cardRepository,
        CardTransactionRepository $cardTransactionRepository
    ) {
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
    function getUserByAuth(){
        $user = auth()->user();
        return $user;
    }
    function logoutFromClub($user){
        // $user=$this->getUserByAuth();
        // if(!$user){
        //     throw new UnauthenticatedException();
        // }
        $card=$this->cardRepository->findCardByUserID($user);
        if(!$card){
            throw new CardTransactionNotFoundException();
        }
        $cardTransaction=$this->cardTransactionRepository->findCardTransactionByCardID($card);
        if(!$cardTransaction||$cardTransaction->type=="Exit"){
            throw new UserAlreadyNotExistInClubException();
        }
        $cardTransaction=$this->cardTransactionRepository->createCardTransaction($card,"Exit");
        return $cardTransaction;

    }
 }