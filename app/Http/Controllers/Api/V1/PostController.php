<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $posts = Post::whereNull('deleted_at')->get();
            return $this->apiResponse(Constants::SUCCESSFUL_RETRIEVAL, $posts, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:100|unique:posts',
            'body' => 'required|string',
            'status' => 'nullable|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $post = Post::create($validator->validated());
            return $this->apiResponse(Constants::SUCCESSFUL_CREATION, $post, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                return $this->apiResponse(Constants::SUCCESSFUL_DISPLAY, $post, Constants::SUCCESS_CODE);
            }
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('posts')->ignore($id)
            ],
            'body' => 'nullable|string',
            'status' => 'nullable|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $post = Post::find($id);

            if ($post) {
                $post->update($validator->validated());
                return $this->apiResponse(Constants::SUCCESSFUL_UPDATE, $post, Constants::SUCCESS_CODE);
            }

            return $this->apiResponse(Constants::OPERATION_FAILED . ': Post not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                $post->delete();
                return $this->apiResponse(Constants::SUCCESSFUL_ARCHIVING, $post, Constants::SUCCESS_CODE);
            }
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function trashed()
    {
        try {
            $posts = Post::onlyTrashed()->get();
            return $this->apiResponse(Constants::SUCCESSFUL_ARCHIVED_DATA_RETRIEVAL, $posts, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function restore($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->restore();
                return $this->apiResponse(Constants::SUCCESSFUL_RESTORATION, $post, Constants::SUCCESS_CODE);
            }
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function forceDelete($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->forceDelete();
                return $this->apiResponse(Constants::SUCCESSFUL_DELETION, null, Constants::SUCCESS_CODE);
            }
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }
}
