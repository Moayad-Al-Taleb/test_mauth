<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use App\Constants\Constants;

trait ApiResponse
{
    /**
     * Get a standardized success response.
     *
     * @param string|null $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(string $message = null, $data = null)
    {
        return response()->json([
            'code' => Constants::SUCCESS_CODE,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Get a standardized error response.
     *
     * @param string $message
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $message, \Exception $exception)
    {
        $errorMessage = $message . ': ' . $exception->getMessage();
        Log::error($errorMessage);

        return response()->json([
            'code' => Constants::ERROR_CODE,
            'message' => $message,
            'error' => $exception->getMessage(), // Include detailed error message
            'data' => null,
        ]);
    }
}
