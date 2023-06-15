<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/task', [TaskController::class, 'store']);
    Route::put('/task/{id}', [TaskController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/user/{id}', [UserController::class, 'updateUser']);
    Route::delete('/task/{id}', [TaskController::class, 'delete']);
});
