<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Logger extends Model
{

    public static function boot() {
        parent::boot();

        static::created(

            function ($model) {
                $logDir = storage_path('logs');
                $logPath = $logDir . '/app.log';

                // Ensure the logs directory exists
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0755, true);
                }

                $timestamp = now()->format('Y-m-d H:i:s');
                $level = strtoupper($model->level);
                $message = $model->message;
                $context = $model->context ? json_encode($model->context, JSON_PRETTY_PRINT) : '[]';

                $logEntry = sprintf(
                    "\n[%s]      |        %s:       %s\nContext: %s\n%s\n",
                    $timestamp,
                    $level,
                    $message,
                    $context,
                    str_repeat('-', 50)
                );

                try {
                    file_put_contents($logPath, $logEntry, FILE_APPEND);
                } catch (\Exception $e) {
                    // Fallback to Laravel's Log if file writing fails
                    Log::error('Failed to write to log file: ' . $e->getMessage());
                }
            }
        );
    }
    protected $fillable = [
        'level',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];


    public static function info(string $message, array $context = []): ?Logger
    {
        $context = self::addUserInfo($context);
        return Logger::create([
            'level' => 'info',
            'message' => $message,
            'context' => $context
        ]);
    }

    public static function error(string $message, array $context = []): ?Logger
    {
        $context = self::addUserInfo($context);
        return Logger::create([
            'level' => 'error',
            'message' => $message,
            'context' => $context
        ]);
    }

    public static function warning(string $message, array $context = []): ?Logger
    {
        $context = self::addUserInfo($context);
        return Logger::create([
            'level' => 'warning',
            'message' => $message,
            'context' => $context
        ]);
    }

    public static function critical(string $message, array $context = []): ?Logger
    {
        $context = self::addUserInfo($context);
        return Logger::create([
            'level' => 'critical',
            'message' => $message,
            'context' => $context
        ]);
    }

    /**
     * Add user firstname and lastname to log context if user is authenticated
     */
    private static function addUserInfo(array $context = []): array
    {
        if (auth()->check()) {
            $user = auth()->user();
            $context['user'] = [
                'id' => $user->id,
                'firstname' => $user->first_name ?? 'N/A',
                'lastname' => $user->last_name ?? 'N/A',
                'email' => $user->email ?? 'N/A'
            ];
        }
        return $context;
    }
}
