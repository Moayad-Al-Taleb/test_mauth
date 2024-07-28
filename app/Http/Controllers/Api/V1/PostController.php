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

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        try {
            // Get the requested language, or use the default locale
            $language = $request->get('lang', app()->getLocale());

            // Fetch all posts and translate title and body based on the requested language
            $posts = Post::all()->map(function ($post) use ($language) {
                $translatedPost = $post->toArray();
                $translatedPost['title'] = $post->getTranslation('title', $language);
                $translatedPost['body'] = $post->getTranslation('body', $language);
                return $translatedPost;
            });

            // Return a successful response with the translated posts
            return $this->apiResponse(Constants::SUCCESSFUL_RETRIEVAL, $posts, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'title_ar' => 'required|string|max:100|unique:posts,title->ar',
            'title_en' => 'required|string|max:100|unique:posts,title->en',
            'body_ar' => 'required|string',
            'body_en' => 'required|string',
            'status' => 'nullable|in:0,1',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Return an error response if validation fails
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            // Create a new post with translations
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

            // Return a successful response with the created post
            return $this->apiResponse(Constants::SUCCESSFUL_CREATION, $post, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Display the specified post.
     */
    public function show(Request $request, $id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                // Get the requested language, or use the default locale
                $language = $request->get('lang', app()->getLocale());

                // Translate title and body based on the requested language
                $translatedPost = $post->toArray();
                $translatedPost['title'] = $post->getTranslation('title', $language);
                $translatedPost['body'] = $post->getTranslation('body', $language);

                // Return a successful response with the translated post
                return $this->apiResponse(Constants::SUCCESSFUL_DISPLAY, $translatedPost, Constants::SUCCESS_CODE);
            }
            // Return an error response if the post is not found
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
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

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Return an error response if validation fails
            return $this->apiResponse('Validation Error: ' . $validator->errors(), null, Constants::ERROR_CODE);
        }

        try {
            $post = Post::find($id);

            if ($post) {
                // Update translations for title and body if provided
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

                // Update status if provided
                if ($request->has('status')) {
                    $post->status = $request->input('status');
                }

                $post->save();

                // Return a successful response with the updated post
                return $this->apiResponse(Constants::SUCCESSFUL_UPDATE, $post, Constants::SUCCESS_CODE);
            }

            // Return an error response if the post is not found
            return $this->apiResponse(Constants::OPERATION_FAILED . ': Post not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            if ($post) {
                $post->delete();
                // Return a successful response with the deleted post
                return $this->apiResponse(Constants::SUCCESSFUL_ARCHIVING, $post, Constants::SUCCESS_CODE);
            }
            // Return an error response if the post is not found
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Display a listing of trashed posts.
     */
    public function trashed(Request $request)
    {
        try {
            // Get the requested language, or use the default locale
            $language = $request->get('lang', app()->getLocale());

            // Fetch all trashed posts and translate title and body based on the requested language
            $posts = Post::onlyTrashed()->get();
            $posts->transform(function ($post) use ($language) {
                $translatedPost = $post->toArray();
                $translatedPost['title'] = $post->getTranslation('title', $language);
                $translatedPost['body'] = $post->getTranslation('body', $language);
                return $translatedPost;
            });

            // Return a successful response with the translated trashed posts
            return $this->apiResponse(Constants::SUCCESSFUL_ARCHIVED_DATA_RETRIEVAL, $posts, Constants::SUCCESS_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Restore the specified trashed post.
     */
    public function restore($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->restore();
                // Return a successful response with the restored post
                return $this->apiResponse(Constants::SUCCESSFUL_RESTORATION, $post, Constants::SUCCESS_CODE);
            }
            // Return an error response if the post is not found
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }

    /**
     * Permanently delete the specified trashed post.
     */
    public function forceDelete($id)
    {
        try {
            $post = Post::onlyTrashed()->find($id);
            if ($post) {
                $post->forceDelete();
                // Return a successful response after permanent deletion
                return $this->apiResponse(Constants::SUCCESSFUL_DELETION, null, Constants::SUCCESS_CODE);
            }
            // Return an error response if the post is not found
            return $this->apiResponse(Constants::OPERATION_FAILED . ': not found', null, Constants::ERROR_CODE);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return $this->apiResponse(Constants::OPERATION_FAILED . ': ' . $e->getMessage(), null, Constants::ERROR_CODE);
        }
    }
}
