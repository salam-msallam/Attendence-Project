<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedHttpException;
use App\Exceptions\ValidationException;
use App\Exceptions\BadRequestHttpException;
use App\Exceptions\UnauthorizedException;

class AuthServices {

    public function loginService($request){
        try{
            $request->validated();
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new UnauthorizedHttpException();
        }
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('access_token')->plainTextToken;
        }catch(ValidationException $e){
            throw new ValidationException($e);
        }catch(BadRequestHttpException $e){
            throw new BadRequestHttpException();
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