<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardControllerTest;


Route::post('/login',AuthController::class.'@login');

Route ::post('/Transaction/{code}',CardTransactionController::class.'@CreateCardTransaction'); //esp

//Attendance-record for Admin by User-id
Route::get('/Attendance_Records_By_UserId/{user_id}',CardTransactionController::class.'@Attendance_Records_By_UserId');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logoutFromClub',CardTransactionController::class.'@logoutFromclub');
    Route::get('/Attendance_Records',CardTransactionController::class.'@Attendance_Records_For_User');//Flutter
    Route::get('/Profile',CardTransactionController::class.'@getTotalMonthlyAttendance');
    Route::post('/logout',AuthController::class.'@Logout');
    
});

    Route::middleware(['auth:sanctum','App\Http\Middleware\AdminMiddleware::class'])->group(function(){

        //User
        Route::apiResource('User',UserController::class);

        //Card
        Route::post('/Card/{user_id}',CardController::class.'@createCardForUser');
    
        Route::get('/Card',CardController::class.'@getAllCards');
    
        Route::get('/Card/{card_id}',CardController::class.'@getCard');
    
        Route::put('/Card/{card_id}',CardController::class.'@updateCard');
    
        Route::delete('/Card/{card_id}',CardController::class.'@deleteCard');

    });
