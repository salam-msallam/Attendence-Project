<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\Auth;
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
            ]);
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
                'password'=>$user->password,
                'role' => $user->role ,
                'token' => $token
            ]
        ]);
        }catch(ValidationException $e){
            return response()->json([
                'code'=>422,
                'message'=>'Data was invalid',
                'errors'=>$e->errors()
            ]);
        }catch(BadRequestHttpException $e){
            return response()->json([
                'code'=>400,
                'message' =>'please check your Json syntax',
                'data'=>null
            ]);
        }
    }

    function logout(){
        Auth::guard('api')->logout(); 

        return response()->json([
            'code'=>200,
            'message' => 'Successfully logged out '
        ]);
    }
}
