<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnauthenticatedException extends Exception
{
    protected $message = 'The email address is already in use ';
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 409,
            'message' => $this->getMessage()
        ]);
    
    }
}
