<?php

use App\Http\Controllers\Api\V1\AuthApplicantController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthProviderController;
use App\Http\Controllers\Api\V1\PostController;
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

Route::prefix('v1')->group(function () {
    Route::group([
        'middleware' => 'api',
        'prefix' => 'auth'
    ], function () {
        // Routes for AuthController (Admin)
        Route::group(['prefix' => 'admin'], function () {
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);
        });

        // Routes for AuthProviderController
        Route::group(['prefix' => 'provider'], function () {
            Route::post('/login', [AuthProviderController::class, 'login']);
            Route::post('/register', [AuthProviderController::class, 'register']);
            Route::post('/logout', [AuthProviderController::class, 'logout']);
            Route::post('/refresh', [AuthProviderController::class, 'refresh']);
            Route::get('/user-profile', [AuthProviderController::class, 'userProfile']);
        });

        // Routes for AuthApplicantController
        Route::group(['prefix' => 'applicant'], function () {
            Route::post('/login', [AuthApplicantController::class, 'login']);
            Route::post('/register', [AuthApplicantController::class, 'register']);
            Route::post('/logout', [AuthApplicantController::class, 'logout']);
            Route::post('/refresh', [AuthApplicantController::class, 'refresh']);
            Route::get('/user-profile', [AuthApplicantController::class, 'userProfile']);
        });
    });
});
