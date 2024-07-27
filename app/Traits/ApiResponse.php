<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use App\Constants\Constants;

trait ApiResponse
{
    /**
     * Get a standardized response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse(string $message, $data = null, int $code)
    {
        if ($code === Constants::ERROR_CODE) {
            Log::error($message);
        }

        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
