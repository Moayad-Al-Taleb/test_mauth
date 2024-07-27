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
            ##### Routes for PostController #####
            Route::get('posts', [PostController::class, 'index'])->name('posts.index');
            Route::post('posts', [PostController::class, 'store'])->name('posts.store');
            Route::get('posts/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
            Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.forceDelete');
            Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
            Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
            Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
            Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update'); // To cover PATCH method
            Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
            ##### End Routes for PostController #####
        });
    });
});
