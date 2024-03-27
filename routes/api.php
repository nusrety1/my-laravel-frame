<?php

use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\Controller::class)->group( function (){
    Route::prefix('user')->group( function () {
        Route::get('/details', 'userDetails');
    });
    Route::prefix('post')->group( function () {
        Route::post('/create', 'postCreate');
    });
    Route::prefix('category')->group( function () {
        Route::post('/create', 'categoryCreate');
    });
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
