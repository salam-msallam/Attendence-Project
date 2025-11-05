<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BadRequestHttpException  extends Exception{
    
    public function render($e): JsonResponse
    {
        return response()->json([
            'code'=>400,
            'message' =>'please check your Json syntax',
            'data'=>null
        ],400);
    
    }
}