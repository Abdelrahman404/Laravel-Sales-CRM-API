<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;

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
    Route::group(['prefix' => 'v1'], function(){
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });

});



Route::group(['prefix' => 'v1/{locale}', 'middleware' => ['auth:api', 'language']], function () {


        // Location routes
        route::get('/countries', [CountryController::Class, 'countries']);
        route::get('/cities/{id}', [CityController::Class, 'cities']);
        route::get('/areas/{id}', [AreaController::Class, 'areas']);

        // Users routes
        route::get('/users', [UserController::Class, 'index']);
        route::get('/users/deleted', [UserController::Class, 'deletedUsers']);
        route::get('/user/{id}', [UserController::Class, 'show']);
        route::post('/user/store', [UserController::Class, 'store']);
        route::post('/user/update/', [UserController::Class, 'update']);
        route::get('/user/delete/{id}', [UserController::Class, 'destroy']);

        // Clients Routes
        route::get('/clients', [ClientController::Class, 'index']);
        // route::get('/users/deleted', [UserController::Class, 'deletedUsers']);
        route::post('/client/store', [ClientController::Class, 'store']);
        route::post('/client/update/', [ClientController::Class, 'update']);
    
        route::get('/user/{id}', [UserController::Class, 'show']);
        route::get('/user/delete/{id}', [UserController::Class, 'destroy']);
        
});

;




