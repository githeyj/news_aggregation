<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/articles', [ArticleController::class, 'index'])
    ->name('articles.index')
    ->middleware('throttle:60,1');

Route::get('/articles/{slug}', [ArticleController::class, 'show'])
    ->name('articles.show')
    ->middleware('throttle:60,1');
