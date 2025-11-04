<?php

use App\Http\Controllers\Api\V2\ExampleController;
use App\Http\Controllers\RedesimController;
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

// Modern API v2 Routes
Route::prefix('v2')->group(function () {
    Route::get('/example', [ExampleController::class, 'index'])
        ->name('api.v2.example.index');
    
    Route::apiResource('examples', ExampleController::class)
        ->names('api.v2.examples');
    
    Route::get('/legacy-integration', [ExampleController::class, 'legacyIntegration'])
        ->name('api.v2.legacy.integration');
});

Route::group(['middleware' => ['redesimAuth']], function () {
    //redesim
    Route::group(['prefix' => 'redesim'], function () {
        Route::post('/companies', [RedesimController::class, 'index'])
            ->name('redesim.companies');
    });
});
