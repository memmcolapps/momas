<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class AppSetting extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'module_id',
        'type',
        'group',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Setting types.
     */
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';
    const TYPE_JSON = 'json';

    /**
     * Common setting groups.
     */
    const GROUP_GENERAL = 'general';
    const GROUP_PAYMENT = 'payment';
    const GROUP_API = 'api';
    const GROUP_FEATURE = 'feature';
    const GROUP_NOTIFICATION = 'notification';
    const GROUP_METER = 'meter';
    const GROUP_TOKEN = 'token';
    const GROUP_SYSTEM = 'system';

    /**
     * Cache configuration.
     */
    const CACHE_PREFIX = 'app_setting_';
    const CACHE_TTL = 60; // minutes
    const CACHE_TAG = 'app_settings';

    /**
     * Get a setting by key (with caching).
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->value;
        });
    }

    /**
     * Set a setting value by key (clears cache after update).
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @param string|null $group
     * @return self
     */
    public static function set(string $key, $value, ?string $description = null, ?string $group = null): self
    {
        $data = ['value' => $value];

        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($group !== null) {
            $data['group'] = $group;
        }

        $setting = static::updateOrCreate(['key' => $key], $data);

        // Clear cache after update
        self::clearCache($key);

        return $setting;
    }

    /**
     * Delete a setting by key (clears cache after deletion).
     *
     * @param string $key
     * @return bool
     */
    public static function remove(string $key): bool
    {
        // Clear cache before deletion
        self::clearCache($key);

        return static::where('key', $key)->delete() > 0;
    }

    /**
     * Check if a setting exists (with caching).
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        $cacheKey = self::CACHE_PREFIX . $key . '_exists';

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($key) {
            return static::where('key', $key)->exists();
        });
    }

    /**
     * Get all settings as key-value array (with caching).
     *
     * @param string|null $group
     * @return array<string, mixed>
     */
    public static function allAsArray(?string $group = null): array
    {
        $cacheKey = self::CACHE_PREFIX . 'all' . ($group ? '_' . $group : '');

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($group) {
            $query = static::where('is_active', true);

            if ($group !== null) {
                $query->where('group', $group);
            }

            return $query->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear cache for a specific key.
     *
     * @param string $key
     * @return void
     */
    public static function clearCache(string $key = null): void
    {
        if ($key !== null) {
            Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . $key);
            Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . $key . '_exists');
        } else {
            // Clear all settings cache
            Cache::tags(self::CACHE_TAG)->flush();
        }

        // Also clear the allAsArray cache
        Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . 'all');
    }

    /**
     * Clear cache when model is saved.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function (self $model) {
            self::clearCache($model->key);
        });

        static::deleted(function (self $model) {
            self::clearCache($model->key);
        });
    }

    /**
     * Scope to get active settings only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope to filter by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the decoded value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if ($this->type === self::TYPE_JSON || $this->type === self::TYPE_ARRAY) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Set the value attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get the display value attribute.
     *
     * @return string
     */
    public function getDisplayValueAttribute(): string
    {
        $value = $this->value;

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return (string) $value;
    }

    /**
     * Determine the type of value automatically.
     *
     * @param mixed $value
     * @return string
     */
    public static function determineType($value): string
    {
        if (is_bool($value)) {
            return self::TYPE_BOOLEAN;
        }

        if (is_int($value)) {
            return self::TYPE_INTEGER;
        }

        if (is_float($value)) {
            return self::TYPE_FLOAT;
        }

        if (is_array($value) || is_object($value)) {
            return self::TYPE_JSON;
        }

        return self::TYPE_STRING;
    }

    /**
     * Get available types.
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_STRING => 'String',
            self::TYPE_INTEGER => 'Integer',
            self::TYPE_FLOAT => 'Float',
            self::TYPE_BOOLEAN => 'Boolean',
            self::TYPE_ARRAY => 'Array',
            self::TYPE_JSON => 'JSON',
        ];
    }

    /**
     * Get available groups.
     *
     * @return array<string, string>
     */
    public static function getGroups(): array
    {
        return [
            self::GROUP_GENERAL => 'General',
            self::GROUP_PAYMENT => 'Payment',
            self::GROUP_API => 'API',
            self::GROUP_FEATURE => 'Feature',
            self::GROUP_NOTIFICATION => 'Notification',
            self::GROUP_METER => 'Meter',
            self::GROUP_TOKEN => 'Token',
            self::GROUP_SYSTEM => 'System',
        ];
    }
}
