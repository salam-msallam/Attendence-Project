<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserAlreadyNotExistInClubException extends Exception{
    
    public function render(): JsonResponse
    {
        return response()->json([
            'code'=>404,
            'message' => 'you are already logout .'
            ]);
    
    }
}