<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginValidateRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthServices;
class AuthController extends Controller
{
    protected $authService;
     
    public function __construct(AuthServices $authService){
        $this->authService = $authService;
    }
    function login(LoginValidateRequest $request){
        $user = $this->authService->loginService($request);
        return response()->json([
            'code' =>200,
            'message'=>'user login successfully',
            'data'=>[
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'phone' => $user['phone'],
                'email' => $user['email'],
                'role' => $user['role'],
                'token' => $user['token']
            ]
        ]);
    }

    function Logout(Request $request){
        $this->authService->logout($request);
        return response()->json([
            'code'=>200,
            'message' => 'Successfully logged out and token revoked '
        ]);
    }
}
