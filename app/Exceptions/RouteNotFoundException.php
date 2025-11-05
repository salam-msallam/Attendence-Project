<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RouteNotFoundException extends AuthenticationException
{
    protected $message = 'Unauthenticated: Token is missing, invalid, Please login again.';
    public function render(): JsonResponse
    {
        return response()->json([
            'code' => 401,
            'message' => $this->getMessage()
        ],401);
    }
}
