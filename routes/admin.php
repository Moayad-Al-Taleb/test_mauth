<?php

use App\Http\Controllers\Api\V1\AuthController;
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

// Route::prefix('v1')->group(function () {
//     Route::get('posts', [PostController::class, 'index']);
//     Route::prefix('admin')->middleware(['jwt.verify'])->group(function () {
//         Route::apiResource('posts', PostController::class)->except(['index']);
//         Route::get('posts/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
//         Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
//         Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.forceDelete');
//     });
// });


Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::middleware(['jwt.verify'])->group(function () {
            ##### Posts Routes #####
            Route::apiResource('posts', PostController::class);
            // Route::get('posts/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
            Route::post('posts/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
            Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
            Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.forceDelete');
            ##### End Posts Routes #####
        });
    });
});
