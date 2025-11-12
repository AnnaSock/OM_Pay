<?php

namespace App\Traits;

use App\Enums\HttpStatusCodes;
use App\Enums\ResponseMessages;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function successResponse($data = null, string $message = ResponseMessages::SUCCESS->value, int $statusCode = HttpStatusCodes::OK->value): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return JsonResponse
     */
    public function errorResponse(string $message = ResponseMessages::SERVER_ERROR->value, int $statusCode = HttpStatusCodes::BAD_REQUEST->value, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}