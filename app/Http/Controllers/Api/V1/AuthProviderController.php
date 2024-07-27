<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthProviderController extends Controller
{
    /**
     * Create a new AuthProviderController instance.
     *
     * Sets middleware to apply to all routes except 'login' and 'register'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:provider', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * Validates credentials and generates a token if data is correct.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^01[0125][0-9]{8}$/'],
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to log in and validate credentials
        if (!$token = auth()->guard('provider')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a new user.
     *
     * Creates a new user and hashes the password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate registration data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:providers',
            'phone' => [
                'required',
                'string',
                'unique:providers',
                'regex:/^01[0125][0-9]{8}$/', // 01012345678
            ],
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Create user and hash the password
        $provider = Provider::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json([
            'message' => 'User has been registered successfully',
            'provider' => $provider
        ], 201);
    }

    /**
     * Log the user out (invalidate the token).
     *
     * Logs the user out and invalidates the current session token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'The user has been successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * Refreshes the user's token and returns the new token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated user's profile.
     *
     * Displays the currently authenticated user's data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->guard('provider')->user());
    }

    /**
     * Get the token structure.
     *
     * Returns the token structure including the token itself, type, expiry, and user information.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'provider' => auth()->guard('provider')->user()
        ]);
    }
}
