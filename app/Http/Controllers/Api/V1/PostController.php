<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;

    // عرض جميع المنشورات غير المؤرشفة
    public function index()
    {
        try {
            $posts = Post::whereNull('deleted_at')->get(); // جلب المنشورات غير المؤرشفة
            return $this->successResponse(Constants::SUCCESSFUL_RETRIEVAL, $posts);
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // إنشاء منشور جديد
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:100|unique:posts',
            'body' => 'required|string',
            'status' => 'nullable|in:0,1',
        ]);

        try {
            $post = Post::create($validatedData);
            return $this->successResponse(Constants::SUCCESSFUL_CREATION, $post);
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // عرض منشور محدد بناءً على المعرف (ID)
    public function show($id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                return $this->successResponse(Constants::SUCCESSFUL_DISPLAY, $post);
            }

            return $this->errorResponse(Constants::OPERATION_FAILED, new \Exception('not found'));
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // تحديث منشور محدد بناءً على المعرف (ID)
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:100|unique:posts,title,' . $id,
            'body' => 'nullable|string',
            'status' => 'nullable|in:0,1',
        ]);

        try {
            $post = Post::find($id);
            if ($post) {
                $post->update($validatedData);
                return $this->successResponse(Constants::SUCCESSFUL_UPDATE, $post);
            }

            return $this->errorResponse(Constants::OPERATION_FAILED, new \Exception('not found'));
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // أرشفة منشور محدد بناءً على المعرف (ID)
    public function archive($id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                $post->delete(); // استخدام الحذف الناعم
                return $this->successResponse(Constants::SUCCESSFUL_ARCHIVING, $post);
            }

            return $this->errorResponse(Constants::OPERATION_FAILED, new \Exception('not found'));
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // عرض جميع المنشورات المؤرشفة
    public function archivedPosts()
    {
        try {
            $posts = Post::onlyTrashed()->get(); // جلب المنشورات المؤرشفة فقط
            return $this->successResponse(Constants::SUCCESSFUL_ARCHIVED_DATA_RETRIEVAL, $posts);
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // استعادة منشور مؤرشف بناءً على المعرف (ID)
    public function restore($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->restore(); // استعادة المنشور المؤرشف
                return $this->successResponse(Constants::SUCCESSFUL_RESTORATION, $post);
            }

            return $this->errorResponse(Constants::OPERATION_FAILED, new \Exception('not found'));
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }

    // حذف منشور نهائيًا بناءً على المعرف (ID)
    public function destroy($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->forceDelete(); // حذف المنشور نهائيًا
                return $this->successResponse(Constants::SUCCESSFUL_DELETION);
            }

            return $this->errorResponse(Constants::OPERATION_FAILED, new \Exception('not found'));
        } catch (\Exception $e) {
            return $this->errorResponse(Constants::OPERATION_FAILED, $e);
        }
    }
}
