<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardTransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardControllerTest;


Route::post('/login',AuthController::class.'@login');

Route ::post('/Transaction/{code}',CardTransactionController::class.'@createCardTransaction'); //esp

//Attendance-record for Admin by User-id
Route::get('/Attendance_Records_By_UserId/{user_id}',CardTransactionController::class.'@Attendance_Records_By_UserId');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logoutFromClub',CardTransactionController::class.'@logoutFromclub');
    Route::get('/attendance_records',CardTransactionController::class.'@Attendance_Records_For_User');//Flutter
    Route::get('/monthlyAttendance',CardTransactionController::class.'@getTotalMonthlyAttendance');
    Route::post('/logoutFromApp',AuthController::class.'@logout');
    Route::get('/profile',UserController::class.'@profile');
    
});


     Route::middleware(['auth:sanctum','App\Http\Middleware\AdminMiddleware::class'])->group(function(){

        //User
     Route::apiResource('User',UserController::class);
    //    Route::post('User', [UserController::class, 'store']); 
    //    Route::get('User', [UserController::class,'index']); 
    //    Route::get('User/{id}', [UserController::class,'show']); 
    //    Route::put('User/{id}', [UserController::class,'update']); 
    //    Route::delete('User/{id}', [UserController::class, 'destroy']); 
        
        //Card
        Route::controller(CardController::class)->group(function(){
    
        Route::post('/Card/{user_id}','createCardForUser');
    
        Route::get('/Card','getAllCards');
    
        Route::get('/Card/{card_id}','getCard');
    
        Route::put('/Card/{card_id}','updateCard');
    
        Route::delete('/Card/{card_id}','deleteCard');
        });

});
   
