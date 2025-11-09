<?php

namespace App\Services;

use App\Exceptions\CardAttendanceNotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\CardRepository;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\ConflictHttpException;
class UserService
{
    protected $userRepository;
    protected $CardRepository;

    public function __construct(UserRepository $userRepository,CardRepository $CardRepository){
        $this->userRepository=$userRepository;
        $this->CardRepository=$CardRepository;
    }

    public function getAllUsers(){
      $AllUsers = $this->userRepository->getAll();
      return $AllUsers;
    }
    public function getSpecificUser($id){
        $user = $this->userRepository->FindUserbyId($id);  
        if (!$user) {
            throw new ModelNotFoundException(); 
        }
        return $user;
    }

    public function deleteUser($id){
        $deleteUser = $this->userRepository->FindUserbyId($id);
         if(!$deleteUser){
          throw new ModelNotFoundException();
        }  
        return $deleteUser;
    }

    public function updateUser($id){
        $updateUser = $this->userRepository->FindUserbyId($id);
         if(!$updateUser){
             throw new ModelNotFoundException();
        }
        return $updateUser;
    }

    public function createNewUser($userData)
    {
        try{
        $user = $this->userRepository->createUser($userData);
        }catch (UniqueConstraintViolationException $e) {
            throw new ConflictHttpException(); 
        }  
        return $user;
    }

    public function profile($user_id){
        $card =  $this->CardRepository->FindCardByUserID($user_id);
        if(!$card){
            throw new CardAttendanceNotFoundException();
        }
        return $card;
    }
        
}