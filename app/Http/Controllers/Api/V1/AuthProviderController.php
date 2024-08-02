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
     * Register a new provider.
     *
     * This function handles the registration of a new provider. It performs
     * the following tasks:
     * - Validates the registration data provided in the request.
     * - Creates a new provider with the validated data.
     * - Hashes the provider's password before storing it.
     * - Assigns the specified role(s) to the new provider.
     * - Returns a JSON response indicating the success or failure of the registration.
     *
     * @param Request $request The HTTP request containing the registration data.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the result of the registration process.
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
                'regex:/^01[0125][0-9]{8}$/', // Egyptian phone numbers: 01012345678
            ],
            'password' => 'required|string|confirmed|min:6',
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Create provider and hash the password
        $provider = Provider::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        // Assign the role with ID 2 to the new provider
        $provider->assignRole(2);

        // Return a JSON response indicating successful registration
        return response()->json([
            'message' => 'Provider has been registered successfully',
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
        auth()->guard('provider')->logout();
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
        return $this->createNewToken(auth()->guard('provider')->refresh());
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
     * Returns the token structure including the token itself, type, expiry, user information, and permissions.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        // Get the currently authenticated provider
        $provider = auth()->guard('provider')->user();

        // Load permissions for the provider
        $permissions = $provider->permissions;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'provider' => $provider,
            'permissions' => $permissions
        ]);
    }
}
