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
        'title',
        'key',
        'value',
        'description',
        'module_id',
        'type',
        'group',
        'is_active',
        'is_global',
        'estate_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'estate_id' => 'integer',
        'value' => 'array',
    ];

    /**
     * The estate this setting belongs to (null when the setting is global).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

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
     * Get a setting by key (with caching). When an estate id is given the
     * estate scoped value is preferred, falling back to the global setting.
     *
     * @param string $key
     * @param mixed $default
     * @param int|null $estateId
     * @return mixed
     */
    public static function get(string $key, $default = null, ?int $estateId = null)
    {
        $cacheKey = self::CACHE_PREFIX . ($estateId !== null ? "estate_{$estateId}_" : '') . $key;

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($key, $default, $estateId) {
            $setting = static::forKey($key, $estateId)->first();

            if (!$setting) {
                return $default;
            }

            return json_decode($setting->value);
        });
    }

    /**
     * Set a setting value by key (clears cache after update).
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $title
     * @param string|null $description
     * @param string|null $group
     * @param bool $isGlobal
     * @param int|null $estateId
     * @return self
     */
    public static function set(string $key, $value, ?string $title = null, ?string $description = null, ?string $group = null, bool $isGlobal = true, ?int $estateId = null): self
    {
        $value = json_encode($value);

        $data = [
            'value' => $value,
            'is_global' => $isGlobal,
            'estate_id' => $isGlobal ? null : $estateId,
        ];

        if ($title !== null) {
            $data['title'] = $title;
        }

        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($group !== null) {
            $data['group'] = $group;
        }

        $setting = static::updateOrCreate(['key' => $key, 'estate_id' => $data['estate_id']], $data);

        // Clear cache after update
        self::clearCache($key, $estateId);

        return $setting;
    }

    /**
     * Delete a setting by key (clears cache after deletion).
     *
     * @param string $key
     * @param int|null $estateId
     * @return bool
     */
    public static function remove(string $key, ?int $estateId = null): bool
    {
        // Clear cache before deletion
        self::clearCache($key, $estateId);

        return static::where('key', $key)->where('estate_id', $estateId)->delete() > 0;
    }

    /**
     * Check if a setting exists (with caching).
     *
     * @param string $key
     * @param int|null $estateId
     * @return bool
     */
    public static function has(string $key, ?int $estateId = null): bool
    {
        $cacheKey = self::CACHE_PREFIX . ($estateId !== null ? "estate_{$estateId}_" : '') . $key . '_exists';

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($key, $estateId) {
            return static::forKey($key, $estateId)->exists();
        });
    }

    /**
     * Get all settings as key-value array (with caching). When an estate id is
     * given both estate scoped and global settings are returned.
     *
     * @param string|null $group
     * @param int|null $estateId
     * @return array<string, mixed>
     */
    public static function allAsArray(?string $group = null, ?int $estateId = null): array
    {
        $cacheKey = self::CACHE_PREFIX . 'all'
            . ($group ? '_' . $group : '')
            . ($estateId !== null ? "_estate_{$estateId}" : '');

        return Cache::tags(self::CACHE_TAG)->remember($cacheKey, self::CACHE_TTL, function () use ($group, $estateId) {
            $query = static::where('is_active', true);

            if ($group !== null) {
                $query->where('group', $group);
            }

            if ($estateId !== null) {
                $query->where(function ($q) use ($estateId) {
                    $q->where('estate_id', $estateId)->orWhereNull('estate_id');
                });
            } else {
                $query->whereNull('estate_id');
            }

            return $query->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear cache for a specific key.
     *
     * @param string|null $key
     * @param int|null $estateId
     * @return void
     */
    public static function clearCache(string $key = null, ?int $estateId = null): void
    {
        $prefix = $estateId !== null ? "estate_{$estateId}_" : '';

        if ($key !== null) {
            Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . $prefix . $key);
            Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . $prefix . $key . '_exists');
        } else {
            // Clear all settings cache
            Cache::tags(self::CACHE_TAG)->flush();
        }

        // Also clear the allAsArray cache
        Cache::tags(self::CACHE_TAG)->forget(self::CACHE_PREFIX . 'all');
    }

    /**
     * Scope a query to a key with optional estate scoping. When an estate id is
     * given estate scoped rows are preferred over global ones.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $key
     * @param int|null $estateId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForKey($query, string $key, ?int $estateId = null)
    {
        $query->where('key', $key);

        if ($estateId !== null) {
            return $query
                ->where(function ($q) use ($estateId) {
                    $q->where('estate_id', $estateId)->orWhereNull('estate_id');
                })
                ->orderByRaw('estate_id IS NULL');
        }

        return $query->whereNull('estate_id');
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
            self::clearCache($model->key, $model->estate_id);
        });

        static::deleted(function (self $model) {
            self::clearCache($model->key, $model->estate_id);
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
