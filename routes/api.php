<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTransactionController;
use App\Http\Controllers\AuthController;


Route::post('/login',AuthController::class.'@login');

Route ::post('/Transaction/{code}',CardTransactionController::class.'@CreateCardTransaction'); //esp

//Attendance-record for Admin by User-id
Route::get('/Attendance_Records_By_UserId/{user_id}',CardTransactionController::class.'@Attendance_Records_By_UserId');

Route::middleware('auth:api')->group(function () {
    Route::get('/user_info', function () {
        return response()->json(auth()->user());
    });
    
    Route::post('/logoutFromClub',CardTransactionController::class.'@logoutFromclub');
    Route::get('/Attendance_Records',CardTransactionController::class.'@Attendance_Records_For_User');//Flutter
    Route::get('/Profile',CardTransactionController::class.'@getTotalMonthlyAttendance');
    Route::post('/logout',AuthController::class.'@Logout');
    
});



    Route::middleware(['auth:api','App\Http\Middleware\AdminMiddleware::class'])->group(function(){
             
        //User
        Route::controller(UserController::class)->group(function(){
            
        Route::post('/User','createUser');
    
        Route::get('/User','getAllUsers');
    
        Route::get('/User/{id}','getUser');
    
        Route::delete('/User/{id}','deleteUser');
    
        Route::put('/User/{id}','updateUser');
        });

        
           //Card
        Route::controller(CardController::class)->group(function(){
    
        Route::post('/Card/{user_id}','createCardForUser');
    
        Route::get('/Card','getAllCards');
    
        Route::get('/Card/{card_id}','getCard');
    
        Route::put('/Card/{card_id}','updateCard');
    
        Route::delete('/Card/{card_id}','deleteCard');
        });

    });

