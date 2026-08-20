<?php

namespace App\Helpers;

class Response
{
    /**
     * Standard API response format
     *
     * @param string $message
     * @param int $status
     * @param bool $success
     * @param mixed $error
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function api($message, $status = 200, $success = true, $error = null, $data = null)
    {
        $response = [
            'message' => $message,
            'status' => $status,
            'success' => $success,
        ];

        if ($error !== null) {
            $response['error'] = $error;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }
}
