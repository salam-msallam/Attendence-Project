<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;

Route::post('/createUser',UserController::class.'@createUser');

Route::get('/getAllUser',UserController::class.'@getAllUsers');

Route::get('/getUser/{id}',UserController::class.'@getUser');

Route::delete('/deleteUser/{id}',UserController::class.'@deleteUser');

Route::put('/updateUser/{id}',UserController::class.'@updateUser');

Route::post('/createCard',CardController::class.'@createCardForUser');

Route::post('/createCard/{user_id}',CardController::class.'@createCardForUser');

Route::get('/getAllcard',CardController::class.'@getAllCards');

Route::get('/getcard/{card_id}',CardController::class.'@getCard');

Route::put('/updateCard/{card_id}',CardController::class.'@updateCard');

Route::delete('/deleteCard/{card_id}',CardController::class.'@deleteCard');