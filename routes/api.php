<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AddBookRatingController;
use App\Http\Controllers\BookReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', RegisterController::class)->name('register');
Route::post('login', LoginController::class)->name('login');
Route::apiResource('books', BookController::class)->except(['store', 'update', 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('session', SessionController::class)->name('session');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::apiResource('users', UserController::class)->except(['store', 'show', 'destroy']);
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('books', BookController::class)->except(['index', 'show']);
    Route::post('books/ratings', AddBookRatingController::class)->name('rating');
    Route::apiResource('reviews', BookReviewController::class);
});
