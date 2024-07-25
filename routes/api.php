<?php

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

// شرح موجز عن Route::apiResource
// Route::apiResource
// يستخدم بشكل رئيسي في تطبيقات الـ API حيث لا تكون واجهات المستخدم التقليدية ضرورية.
// يقوم apiResource بإنشاء مسارات أساسية للتعامل مع البيانات عبر واجهات برمجة التطبيقات، بدون المسارات الإضافية
// المتعلقة بواجهات المستخدم مثل إنشاء وتعديل البيانات.
//
// المسارات التي تم إنشاؤها بواسطة Route::apiResource:
//
// GET /posts - عرض قائمة المنشورات (يستدعي index).
// POST /posts - إنشاء منشور جديد (يستدعي store).
// GET /posts/{id} - عرض منشور معين (يستدعي show).
// PUT/PATCH /posts/{id} - تحديث منشور معين (يستدعي update).
// DELETE /posts/{id} - حذف منشور معين (يستدعي destroy).
//
// المسارات التي لم يتم إنشاؤها بواسطة apiResource:
//
// GET /posts/create - غير متاح
// GET /posts/{id}/edit - غير متاح
//

// مسار للحصول على بيانات المستخدم المصادق عليه باستخدام Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// تخصيص مسارات للإصدار 1 من API
Route::prefix('v1')->group(function () {
    ########## مسارات المنشورات ##########
    Route::prefix('posts')->group(function () {
        // مسارات الموارد الأساسية لـ PostController
        // هذا ينشئ المسارات التالية:
        // GET /v1/posts - عرض جميع المنشورات
        // POST /v1/posts - إنشاء منشور جديد
        // GET /v1/posts/{id} - عرض منشور محدد
        // PUT/PATCH /v1/posts/{id} - تحديث منشور محدد
        // DELETE /v1/posts/{id} - حذف منشور محدد
        Route::apiResource('', PostController::class); // لا حاجة لتكرار 'posts'

        // مسارات مخصصة
        // مسار مخصص لأرشفة منشور
        // PATCH /v1/posts/{id}/archive - أرشفة منشور محدد
        Route::patch('{id}/archive', [PostController::class, 'archive'])->name('posts.archive');

        // مسار مخصص للحصول على المنشورات المؤرشفة
        // GET /v1/posts/archived - عرض جميع المنشورات المؤرشفة
        Route::get('archived', [PostController::class, 'archivedPosts'])->name('posts.archived');

        // مسار مخصص لاستعادة منشور مؤرشف
        // PATCH /v1/posts/{id}/restore - استعادة منشور مؤرشف محدد
        Route::patch('{id}/restore', [PostController::class, 'restore'])->name('posts.restore');

        // مسار مخصص لحذف منشور نهائيًا
        // DELETE /v1/posts/{id}/force-delete - حذف منشور محدد نهائيًا
        Route::delete('{id}/force-delete', [PostController::class, 'destroy'])->name('posts.force-delete');
    });
});
