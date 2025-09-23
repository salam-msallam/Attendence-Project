<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\cardTrasactionController;
use App\Http\Controllers\AuthController;

Route::post('/createUser',UserController::class.'@createUser');

Route::get('/getAllUser',UserController::class.'@getAllUsers');

Route::get('/getUser/{id}',UserController::class.'@getUser');

Route::delete('/deleteUser/{id}',UserController::class.'@deleteUser');

Route::put('/updateUser/{id}',UserController::class.'@updateUser');

Route::post('/createCard',CardController::class.'@createCard');

Route::post('/createCard/{user_id}',CardController::class.'@createCardForUser');

Route::get('/getAllcard',CardController::class.'@getAllCards');

Route::get('/getcard/{card_id}',CardController::class.'@getCard');

Route::put('/updateCard/{card_id}',CardController::class.'@updateCard');

Route::delete('/deleteCard/{card_id}',CardController::class.'@deleteCard');

Route ::post('/creatCardTransaction/{code}',cardTrasactionController::class.'@createCardTransaction'); 

Route::post('/login',AuthController::class.'@login');

Route::middleware('auth:api')->group(function () {
    Route::get('/user_info', function () {
        return response()->json(auth()->user());
    });
});

