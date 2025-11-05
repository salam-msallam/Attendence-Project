<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UniqueConstraintViolationException extends Exception
{
    protected $message = 'The email address is already in use ';
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 409,
            'message' => $this->getMessage()
        ],409);
    
    }
}
