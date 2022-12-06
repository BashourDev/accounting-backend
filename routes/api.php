<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);

    Route::middleware('is_admin')->group(function () {

       Route::prefix('/users')->group(function () {
           Route::get('/', [UserController::class, 'index']);
           Route::post('/create', [UserController::class, 'store']);
           Route::prefix('/{user}')->group(function () {
               Route::get('/', [UserController::class, 'show']);
              Route::put('/update', [UserController::class, 'update']);
               Route::patch('/lock', [UserController::class, 'lockUser']);
               Route::patch('/unlock', [UserController::class, 'unlockUser']);
              Route::delete('/delete', [UserController::class, 'destroy']);
              Route::patch('/update-password', [UserController::class, 'updatePassword']);
              Route::put('/update-permissions', [UserController::class, 'updatePermissions']);
           });
       });

    });

});
