<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ValidationException  extends Exception{
    
    public function render($e): JsonResponse
    {
        return response()->json([
            'code'=>422,
            'message'=>'Data was invalid',
            'errors'=>$e->errors()
            ],422);
    
    }
}