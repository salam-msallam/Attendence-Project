<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
class UserController extends Controller
{
    function createUser(Request $request){
        try{
            $user=User::create([
                "first_name" =>$request->input("first_name"),
                "last_name" =>$request->input("last_name"),
                "Phone"=>$request->input("Phone"),
                "email"=>$request->input("email"),
                "password"=>$request->input("password"),
                "role"=>$request->input("role"),
            ]);
            return $user;

        }catch (UniqueConstraintViolationException $e) { 
            return response()->json([
                'code' => 409, 
                'message' => 'The email address is already in use.',
            ], 409);
            
        } 
        
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
        if($deleteUser){
             $deleteUser->delete();
             return "User deleted";
        }
       return response()->json(['message'=>'User Not Found'],404);
       
    }

    function updateUser(Request $request,$id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message'=>'User Not Found'],404);
        }
        $allRequestData = $request->all();
        $user->update($allRequestData);
        $user->save();
        return response()->json(['message'=>'User Updated successfully'],200);
    }
}

