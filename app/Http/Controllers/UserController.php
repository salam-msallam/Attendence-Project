<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    function createUser(Request $request){
        $user=User::create([
            "name" =>$request->input("name"),
            "email"=>$request->input("email"),
            "number"=>$request->input("number"),
            "password"=>$request->input("password"),
        ]);
        return $user;
    }
    
    function getAllUsers(Request $request){
        $AllUsers=User::all();
        return $AllUsers;
    }

    function getUser($id){
        $user = User::find($id);
        return $user;
    }

    function deleteUser($id){
        $deleteUser=User::find($id);
        $deleteUser->delete();
        return "User deleted";
    }

    function updateUser($id){
        $user = User::find($id);
        $user->Age = "20";
        $user->save();
        return $user;
    }
}
