<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 401,
            'message' => 'Unauthorized.'
        ],401);
    
    }
}
