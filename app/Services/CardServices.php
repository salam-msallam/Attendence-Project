<?php
 
namespace App\Services;
use App\Repository\CardRepository;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\ConflictHttpException;
use App\Exceptions\CardUserFoundException;
use App\Models\Card;
use App\Models\User;


class CardServices {
    protected $cardRepository;
    public function __construct(CardRepository $cardRepository){
        $this->cardRepository=$cardRepository;
    }
    function getAllCards(){
        $AllCards=$this->cardRepository->getAllCards();
        if(!$AllCards){
        throw new ModelNotFoundException();
        }
        return $AllCards; 
       
    }

    function getCard($id){
        $card = $this->cardRepository->findCardByID($id);
        if(!$card){
            throw new ModelNotFoundException();
       }
       return $card;  
            
    }

    function deleteCard($id){
        $deleteCard=$this->cardRepository->findCardByID($id);
        if(!$deleteCard){
            
            throw new ModelNotFoundException();
        }
        return $deleteCard;
    }

    function updateCard($id,array $data){

        $card =$this->cardRepository->updateCard($id,$data);
        if(!$card){
            throw new ModelNotFoundException();
        }
        return $card;   
    }

    function createCardForUser($user_id, array $data){
        $user=User::where('id',$user_id)->first();
        $cardUser=Card::where('user_id',$user_id)->first();
        if(!$user){
            throw new ModelNotFoundException();
        }
        else if($cardUser){
            throw new CardUserFoundException();
        }

        try{
           $card=$this->cardRepository->createCardForUser($user , $data);
           return $card;
        }catch (UniqueConstraintViolationException $e) { 
            throw new ConflictHttpException();
       } 


    }
 





}