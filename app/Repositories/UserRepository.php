<?php

namespace App\Repositories;

use App\Models\User;


class UserRepository{
    public function getAll(){
        return User::all();
    }

    public function FindUserbyId($id){
        return User::find($id);
    }

    public function createUser($data)
    {
        return User::create($data);
    }
    
}