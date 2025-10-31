<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedHttpException extends Exception{
    
    public function render(): JsonResponse
    {
        return response()->json([
            'code'=>401,
            'message' => 'Unauthorized: Invalid email or password.'
            ]);
            
    
    }
}