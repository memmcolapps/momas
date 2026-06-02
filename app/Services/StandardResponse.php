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

    public static function error($code = 500, $message = 'An error occurred', $data = [], $debug = null) {
        $errorString = '';
        $data = $data ?? [];

        $validationMessages = ['validation error', 'validator error', 'validation_error'];
        $isValidationError = in_array(strtolower($data['message'] ?? $message), $validationMessages);

        // dd($isValidationError);

        if (is_array($data) && isset($data['errors'])) {
            $allErrors = [];
            foreach ($data['errors'] as $field => $messages) {
                if (is_array($messages)) {
                    $allErrors = array_merge($allErrors, $messages);
                } else {
                    $allErrors[] = $messages;
                }
            }
            $errorString = implode(' | ', $allErrors);
        } elseif (is_array($data) && isset($data['error'])) {
            $error = $data['error'];
            unset($data['error']);

            if (is_object($error) && method_exists($error, 'toArray')) {
                $error = $error->toArray();
            }

            if ($isValidationError && is_array($error)) {
                // Flatten nested arrays (e.g. ['field' => ['msg1', 'msg2'], ...])
                $allErrors = [];
                foreach ($error as $field => $messages) {
                    if (is_array($messages)) {
                        $allErrors = array_merge($allErrors, $messages);
                    } else {
                        $allErrors[] = $messages;
                    }
                }
                $errorString = implode(' | ', $allErrors);
                $message = $errorString;
            } elseif (is_array($error)) {
                $errorString = implode(' | ', array_values($error));
            } else {
                $errorString = $error;
            }
        } elseif (is_string($data)) {
            $errorString = $data;
        }

        if (isset($data['errors'])) {
            unset($data['errors']);
        }

        if (in_array('message', array_keys($data ?? []))) {
            $message = $data['message'];
        }

        $error = env('APP_DEBUG', false) === true ? [
                'error' => $errorString,
                'line' => $data['line'] ?? null,
                'file' => $data['file'] ?? null,
            ] : [];

        unset($data['line'], $data['file'], $data['message']);

        return response()->json([
            'status' => 'error',
            'status_code' => $code,
            'message' => $message,
            'data' => [
                ...$data,
                ...$error,
                in_array(env('APP_ENV', 'staging'), ['stg', 'staging', 'stage']) && 'debug' => $debug,
            ]
        ], $code);
    }

    // public static function error($code = 400, $message = 'Error', $data = null, )
    // {
    //     return response()->json([
    //         'status' => false,
    //         'message' => $message,
    //         'data' => $data,

    //     ], $code);
    // }

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
