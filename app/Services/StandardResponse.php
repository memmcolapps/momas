<?php

namespace App\Services;

class StandardResponse
{
    public static function success($code = 200, $message = 'Success', $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($code = 400, $message = 'Error', $data = null, $debug = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
            in_array(env('APP_ENV', 'staging'), ['stg', 'staging', 'stage']) && 'debug' => $debug,
        ], $code);
    }

    public static function paginated($items, $total, $page, $perPage, $message = 'Success')
    {
        return [
            'success' => true,
            'code' => 200,
            'message' => $message,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'pages' => ceil($total / $perPage),
            ],
        ];
    }
}
