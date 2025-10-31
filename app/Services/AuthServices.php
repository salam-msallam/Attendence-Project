<?php

namespace App\Services;
use App\Repositories\AuthRepositories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Exceptions\UnauthenticatedException;
use App\Exceptions\UnauthorizedException;

class AuthServices {
    
    public function loginService($request){
        try{
            $request->validated();
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'code'=>401,
                    'message' => 'Unauthorized: Invalid email or password.'
                ]);
        }
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('access_token')->plainTextToken;
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
        return [
            'id' => $user->id,
            'first_name' => $user->first_name ,
            'last_name' => $user->last_name ,
            'phone' => $user->Phone ,
            'email' => $user->email,
            'role' => $user->role ,
            'token'=>$token
        ];
    }

    public function logout($request){
         if (!$request->user()) {
            throw new UnauthorizedException();
        }
        $request->user()->currentAccessToken()->delete();
    }
}