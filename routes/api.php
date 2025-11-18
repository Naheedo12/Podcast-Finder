<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Podcasts routes
    Route::get('/podcasts', [PodcastController::class, 'index']);
    Route::get('/podcasts/{id}', [PodcastController::class, 'show']);
    Route::post('/podcasts', [PodcastController::class, 'store']);
    Route::post('/podcasts/{podcast}', [PodcastController::class, 'update']);
    Route::delete('/podcasts/{podcast}', [PodcastController::class, 'destroy']);

    // Episodes routes
    Route::get('/podcasts/{podcast_id}/episodes', [EpisodeController::class, 'index']);
    Route::get('/episodes/{id}', [EpisodeController::class, 'show']);
    Route::post('/podcasts/{podcast_id}/episodes', [EpisodeController::class, 'store']);
    Route::post('/episodes/{episode}', [EpisodeController::class, 'update']);
    Route::delete('/episodes/{episode}', [EpisodeController::class, 'destroy']);

    // Hosts routes
    Route::get('/hosts', [UserController::class, 'hosts']);
    Route::get('/hosts/{id}', [UserController::class, 'showHost']);

    // Users management (Admin only)
    Route::get('/users', [UserController::class, 'allUsers']);
    Route::post('/users', [UserController::class, 'storeUser']);
    Route::put('/users/{id}', [UserController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']);

    // Search routes
    Route::get('/search/podcasts', [PodcastController::class, 'search']);
    Route::get('/search/episodes', [EpisodeController::class, 'search']);
});