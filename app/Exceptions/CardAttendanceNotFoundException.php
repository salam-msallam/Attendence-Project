<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CardAttendanceNotFoundException extends Exception{
    
    public function render(): JsonResponse
    {
        return response()->json([
            'code'=>404,
            'message' => 'No attendance card found for this user.'
            ],404);
            
    }
}