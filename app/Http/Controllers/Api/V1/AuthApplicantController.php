<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthApplicantController extends Controller
{
    /**
     * Create a new AuthApplicantController instance.
     *
     * Sets middleware to apply to all routes except 'login' and 'register'.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:applicant', ['except' => ['login', 'register']]);
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
        if (!$token = auth()->guard('applicant')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a new applicant.
     *
     * This function handles the registration of a new applicant. It performs
     * the following tasks:
     * - Validates the registration data provided in the request.
     * - Creates a new applicant with the validated data and hashes their password.
     * - Assigns the specified role to the new applicant.
     * - Returns a JSON response indicating the success or failure of the registration process.
     *
     * @param Request $request The HTTP request containing the registration data.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the result of the registration process.
     */
    public function register(Request $request)
    {
        // Validate registration data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:applicants',
            'phone' => [
                'required',
                'string',
                'unique:applicants',
                'regex:/^01[0125][0-9]{8}$/', // Egyptian phone numbers: 01012345678
            ],
            'password' => 'required|string|confirmed|min:6',
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Create applicant and hash the password
        $applicant = Applicant::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        // Assign the role with ID 3 to the new applicant
        $applicant->assignRole(3);

        // Return a JSON response indicating successful registration
        return response()->json([
            'message' => 'Applicant has been registered successfully',
            'applicant' => $applicant
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
        auth()->guard('applicant')->logout();
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
        return $this->createNewToken(auth()->guard('applicant')->refresh());
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
        return response()->json(auth()->guard('applicant')->user());
    }

    /**
     * Get the token structure.
     *
     * Returns the token structure including the token itself, type, expiry, applicant information, and permissions.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        // Get the currently authenticated applicant
        $applicant = auth()->guard('applicant')->user()->makeHidden(['permissions']);

        // Load permissions for the applicant
        $permissions = $applicant->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name
            ];
        });

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'applicant' => $applicant,
            'permissions' => $permissions
        ]);
    }
}
