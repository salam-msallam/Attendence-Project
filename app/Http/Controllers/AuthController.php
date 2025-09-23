<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    function login(Request $request){
        try{
        $request->validate(['email'=>'required|email','password'=>'required']);
        $login=$request->only(['email','password']);
        $token = JWTAuth::attempt($login);
        if(!$token){
            return response()->json([
                'code' =>'401',
                'message'=>'Unauthorized',
                'data'=>null
            ],401);
        }
        $user=auth()->user();
        return response()->json([
            'code' =>'200',
            'message'=>'user login successfully',
            'data'=>[
                'id' => $user->id,
                'first_name' => $user->first_name ,
                'last_name' => $user->last_name ,
                'phone' => $user->Phone ,
                'email' => $user->email,
                'role' => $user->role ,
                'token' => $token
            ]
        ]);
    }catch(ValidationException $e){
        return response()->json([
            'code'=>422,
            'message'=>'Data was invalid',
            'errors'=>$e->errors()
        ],422);
    }catch(BadRequestHttpException $e){
        return response()->json([
            'code'=>400,
            'message' =>'please check your Json syntax',
            'data'=>null
        ],400);
    }
}
}
