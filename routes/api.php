<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [UserController::class, 'authenticate'])->name('login');
Route::post('register', [UserController::class, 'register']);
Route::post('confirm-register', [UserController::class, 'confirmRegister']);
Route::group(['middleware' => 'jwt.verify'], function(){
    Route::post('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/send-invitation', [AdminController::class, 'sendInvitation']);

    Route::post('/update-profile', [UserController::class, 'updateProfile']);
});
