<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});



Route::group(['prefix' => 'v1/{locale}', 'middleware' => ['auth:api', 'language']], function () {


        // Location routes
        route::get('/countries', [LocationController::Class, 'countries']);
        route::get('/cities/{id}', [LocationController::Class, 'cities']);
        route::get('/areas/{id}', [LocationController::Class, 'areas']);

        // Users routes
        route::get('/users', [UserController::Class, 'index']);
        route::get('/user/{id}', [UserController::Class, 'show']);
        route::post('/user/store', [UserController::Class, 'store']);
        route::post('/user/update/{id}', [UserController::Class, 'update']);
        
    
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});




