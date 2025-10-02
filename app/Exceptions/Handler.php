<?php 
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Database\QueryException; 
use Illuminate\Database\UniqueConstraintViolationException; 
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;


class Handler extends ExceptionHandler{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => 'Token is Invalid'], 400);
        } else if ($exception instanceof TokenExpiredException) {
            return response()->json(['error' => 'Token is Expired'], 400);
        } else if ($exception instanceof TokenBlacklistedException) {
            return response()->json(['error' => 'Token is Blacklisted'], 400);
        } else if ($exception instanceof UniqueConstraintViolationException) {
            return response()->json([
                'error' => 'Must be Unique',
                'message' => 'The data provided (card code) is already in use.'
            ], 409); // 409 Conflict
        }
    
        // ⬇️ معالجة QueryException (كخيار احتياطي للـ 1062)
       else if ($exception instanceof QueryException) {
            if ($exception->errorInfo[1] === 1062) {
                return response()->json([
                    'error' => 'Must be Unique',
                    'message' => 'The data provided is already in use (code 1062).'
                ], 409);
            }
        }
    
        return parent::render($request, $exception);
    }
}
