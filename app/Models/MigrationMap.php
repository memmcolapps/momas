<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrationMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_name',
        'legacy_key',
        'mapped_value',
    ];

    public function scopeMapName($query, string $mapName)
    {
        return $query->where('map_name', $mapName);
    }

    /**
     * Load a map as an associative array [legacy_key => mapped_value].
     */
    public static function loadMap(string $mapName): array
    {
        return static::where('map_name', $mapName)
            ->pluck('mapped_value', 'legacy_key')
            ->toArray();
    }

    /**
     * Upsert a single mapping entry.
     */
    public static function setMapping(string $mapName, string $legacyKey, string $mappedValue): static
    {
        return static::updateOrCreate(
            ['map_name' => $mapName, 'legacy_key' => $legacyKey],
            ['mapped_value' => $mappedValue]
        );
    }

    /**
     * Upsert multiple mapping entries at once.
     */
    public static function setMappings(string $mapName, array $mappings): void
    {
        foreach ($mappings as $legacyKey => $mappedValue) {
            static::updateOrCreate(
                ['map_name' => $mapName, 'legacy_key' => (string) $legacyKey],
                ['mapped_value' => (string) $mappedValue]
            );
        }
    }

    /**
     * Check if a key exists in a map.
     */
    public static function hasMapping(string $mapName, string $legacyKey): bool
    {
        return static::where('map_name', $mapName)
            ->where('legacy_key', $legacyKey)
            ->exists();
    }

    /**
     * Get all legacy keys for a given map.
     */
    public static function getKeys(string $mapName): array
    {
        return static::where('map_name', $mapName)
            ->pluck('legacy_key')
            ->toArray();
    }

    /**
     * Clear all entries for a given map.
     */
    public static function clearMap(string $mapName): void
    {
        static::where('map_name', $mapName)->delete();
    }

    /**
     * Clear multiple maps at once.
     */
    public static function clearMaps(array $mapNames): void
    {
        static::whereIn('map_name', $mapNames)->delete();
    }
}
