<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends Exception
{
    protected $message = 'Not Found';
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 404,
            'message' => $this->getMessage()
        ]);
    
    }
}
