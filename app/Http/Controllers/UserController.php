<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    function createUser(Request $request){
        $user=User::create([
            "first_name" =>$request->input("first_name"),
            "last_name" =>$request->input("last_name"),
            "Phone"=>$request->input("Phone"),
            "email"=>$request->input("email"),
            "password"=>$request->input("password"),
            "role"=>$request->input("role"),
        ]);
        return $user;
    }
    
    function getAllUsers(){
        $AllUsers=User::all();
        return $AllUsers;
    }

    function getUser($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message'=>'User Not Found'],404);
        }
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
