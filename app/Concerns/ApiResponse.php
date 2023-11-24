<?php

namespace App\Concerns;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function generateResponse($data, $additional = false, string $message = '')
    {

        return response()->json([
            'message' => $message,
            'data' => $additional ? $data->items() : $data,
            'additional' => $additional ? $this->getAdditional($data) : null,
        ]);
    }

    public function generateErrorResponse($data, string $message = '', int $code = Response::HTTP_BAD_REQUEST)
    {

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function getAdditional($data): array
    {
        try {
            return [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
