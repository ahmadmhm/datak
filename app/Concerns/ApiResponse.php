<?php

namespace App\Concerns;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function generateResponse($data, $additional = false, string $message = '')
    {

        return response()->json([
            'message' => $message,
            'data' => $data,
            'additional' => $additional ? ['count' => count($data)] : null,
        ]);
    }

    public function generateErrorResponse($data, string $message = '', int $code = Response::HTTP_BAD_REQUEST)
    {

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
