<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrationState extends Model
{
    use HasFactory;

    protected $fillable = [
        'context',
        'module',
        'status',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public function scopeContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public static function markCompleted(string $context, string $module, array $stats = []): static
    {
        return static::updateOrCreate(
            ['context' => $context, 'module' => $module],
            ['status' => 'completed', 'stats' => $stats]
        );
    }

    public static function getCompletedModules(string $context): array
    {
        return static::where('context', $context)
            ->where('status', 'completed')
            ->pluck('module')
            ->toArray();
    }

    public static function clearContext(string $context): void
    {
        static::where('context', $context)->delete();
    }
}
