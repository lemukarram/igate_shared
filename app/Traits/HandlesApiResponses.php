<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

trait HandlesApiResponses
{
    /**
     * Success Response
     */
    protected function successResponse($data, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error Response
     */
    protected function errorResponse($message = 'Error', $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Handle Exception and return response
     */
    protected function handleException(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->errorResponse(
                $e->getMessage(),
                422,
                $e->errors()
            );
        }

        // Handle database unique constraint violation if validation missed it
        if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == 23000) {
            return $this->errorResponse(
                'Duplicate data entry detected. This record might already exist.',
                409
            );
        }

        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        
        // Ensure valid HTTP status code
        if ($code < 100 || $code >= 600) {
            $code = 500;
        }

        return $this->errorResponse(
            $e->getMessage() ?: 'An unexpected error occurred',
            $code,
            config('app.debug') ? [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ] : null
        );
    }
}
