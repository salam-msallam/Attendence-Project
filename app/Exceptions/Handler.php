<?php 
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

public function render($request, Throwable $exception)
{
    if ($exception instanceof TokenInvalidException) {
        return response()->json(['error' => 'Token is Invalid'], 400);
    } else if ($exception instanceof TokenExpiredException) {
        return response()->json(['error' => 'Token is Expired'], 400);
    } else if ($exception instanceof TokenBlacklistedException) {
        return response()->json(['error' => 'Token is Blacklisted'], 400);
    }

    return parent::render($request, $exception);
}