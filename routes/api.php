<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/logout', [AuthController::class,'logout']);
    Route::apiResource('/products', ProductController::class);
    Route::post('/comments/store', [CommentController::class,'store']);
    Route::delete('/comments/destroy/{comment}', [CommentController::class,'destroy']);
    Route::get('/search', [ProductController::class,'search']);

});
