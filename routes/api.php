<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTransactionController;
use App\Http\Controllers\AuthController;


// Route::delete('/logout', AuthController::class, '@logout');

Route::post('/login',AuthController::class.'@login');

Route ::post('/Transaction/{code}',CardTransactionController::class.'@CreateCardTransaction'); //esp



Route::middleware('auth:api')->group(function () {
    Route::get('/user_info', function () {
        return response()->json(auth()->user());
    });
    
    Route::post('/logout',CardTransactionController::class.'@logoutFromclub');
    Route::get('/Attendance_Records',CardTransactionController::class.'@Attendance_Records_For_User');//Flutter
    Route::get('/Profile',CardTransactionController::class.'@getTotalMonthlyAttendance');
    Route::post('/logout',AuthController::class.'@Logout');
    
});

Route::middleware(['auth:api','App\Http\Middleware\AdminMiddleware::class'])->group(function(){

    //User
    Route::post('/User',UserController::class.'@createUser');

    Route::get('/User',UserController::class.'@getAllUsers');

    Route::get('/User/{id}',UserController::class.'@getUser');

    Route::delete('/User/{id}',UserController::class.'@deleteUser');

    Route::put('/User/{id}',UserController::class.'@updateUser');

    //Card
    Route::post('/Card/{user_id}',CardController::class.'@createCardForUser');

    Route::get('/Card',CardController::class.'@getAllCards');

    Route::get('/Card/{card_id}',CardController::class.'@getCard');

    Route::put('/Card/{card_id}',CardController::class.'@updateCard');

    Route::delete('/Card/{card_id}',CardController::class.'@deleteCard');
});

