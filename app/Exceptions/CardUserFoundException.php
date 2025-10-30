<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CardUserFoundException extends Exception
{
    protected $message = "There is already card for this User";
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 409,
            'message' => $this->getMessage()
        ]);
    
    }
}