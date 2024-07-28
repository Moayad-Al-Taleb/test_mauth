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

    public function index(Request $request)
    {
        try {
            $language = $request->get('lang', app()->getLocale());

            $posts = Post::all()->map(function ($post) use ($language) {
                $post->title = $post->getTranslation('title', $language);
                $post->body = $post->getTranslation('body', $language);
                return $post;
            });

            return $this->apiResponse(Constants::SUCCESSFUL_RETRIEVAL, $posts, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'title_ar' => 'required|string|max:100|unique:posts,title->ar',
            'title_en' => 'required|string|max:100|unique:posts,title->en',
            'body_ar' => 'required|string',
            'body_en' => 'required|string',
            'status' => 'nullable|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $post = new Post();
            $post->setTranslations('title', [
                'ar' => $request->input('title_ar'),
                'en' => $request->input('title_en'),
            ]);
            $post->setTranslations('body', [
                'ar' => $request->input('body_ar'),
                'en' => $request->input('body_en'),
            ]);
            $post->status = $request->input('status', '0');
            $post->save();

            return $this->apiResponse(Constants::SUCCESSFUL_CREATION, $post, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                $language = $request->get('lang', app()->getLocale());

                $post->title = $post->getTranslation('title', $language);
                $post->body = $post->getTranslation('body', $language);

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
            'title_ar' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('posts', 'title->ar')->ignore($id)
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('posts', 'title->en')->ignore($id)
            ],
            'body_ar' => 'nullable|string',
            'body_en' => 'nullable|string',
            'status' => 'nullable|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $post = Post::find($id);

            if ($post) {
                if ($request->has('title_ar')) {
                    $post->setTranslation('title', 'ar', $request->input('title_ar'));
                }
                if ($request->has('title_en')) {
                    $post->setTranslation('title', 'en', $request->input('title_en'));
                }
                if ($request->has('body_ar')) {
                    $post->setTranslation('body', 'ar', $request->input('body_ar'));
                }
                if ($request->has('body_en')) {
                    $post->setTranslation('body', 'en', $request->input('body_en'));
                }

                if ($request->has('status')) {
                    $post->status = $request->input('status');
                }

                $post->save();

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

    public function trashed(Request $request)
    {
        try {
            $language = $request->get('lang', app()->getLocale());

            $posts = Post::onlyTrashed()->get();

            $posts->transform(function ($post) use ($language) {
                $post->title = $post->getTranslation('title', $language);
                $post->body = $post->getTranslation('body', $language);
                return $post;
            });

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
