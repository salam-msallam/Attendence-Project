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
            return response()->json([
                'code'=>200,
                'message'=>'Create user Successfully',
                'data'=>[
                    'User'=>$user
                ]
                ]);
        
        }catch (UniqueConstraintViolationException $e) { 
            return response()->json([
                'code' => 409, 
                'message' => 'The email address is already in use.',
            ],);
            
        } 
        
    }

    
    function getAllUsers(){
        $AllUsers=User::all();
        if($AllUsers){
        return response()->json([
            'code'=>200,
            'message'=>'Get All users Successfully',
            'data'=>[
                'All User'=>$AllUsers
            ]
            ]);
        }
        
    }

    function getUser($id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'code'=>404,
                'message'=>'User Not Found'
            ]);
        }
        return response()->json([
            'code'=>200,
            'message'=>'Get  user Successfully',
            'data'=>[
                'user'=>$user
            ]
        ]);
    }

    function deleteUser($id){
        $deleteUser=User::find($id);
        if($deleteUser){
             $deleteUser->delete();
             return response()->json([
                'code'=>200,
                'message'=>'delete user Successfully '
            ]);
        }
       return response()->json([
        'code'=>404,
        'message'=>'User Not Found'
    ]);
       
    }

    function updateUser(Request $request,$id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'code'=>404,
                'message'=>'User Not Found']);
        }
        $allRequestData = $request->all();
        $user->update($allRequestData);
        $user->save();
        return response()->json([
            'card'=>200,
            'message'=>'user Updated successfully',
            'data'=>[
                'user'=>$user
            ]
        ]);
    }
}

