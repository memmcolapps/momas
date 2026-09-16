<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Estate;
use App\Models\Setting;
use Exception;

class ConfigManagementService {

    /**
     * Ordered list of resolution providers. Each maps to a private method that
     * resolves a value or returns null when the key is not found there.
     *
     * @var array<string, string>
     */
    private const PROVIDERS = [
        'appSetting' => 'resolveFromAppSetting',
        'setting' => 'resolveFromSetting',
        'config' => 'resolveFromConfig',
        'env' => 'resolveFromEnv',
    ];

    public function __construct() {

    }

    /**
     * Resolve a configuration value. The key is searched across every provider
     * in order of priority; the first provider that has a value wins.
     *
     * @param string $key
     * @param int|null $estateId
     * @return mixed
     */
    public function getConfig(string $key, ?int $estateId = null) {
        if (empty($key)) {
            throw new Exception('A valid key must be passed to get config.');
        }

        if ($estateId !== null && ! Estate::where('id', $estateId)->exists()) {
            throw new Exception("Invalid estate id '{$estateId}' passed to get config.");
        }

        foreach (self::PROVIDERS as $method) {
            $value = $this->{$method}($key, $estateId);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Persist a configuration value into the app settings.
     *
     * @param string $key
     * @param mixed $value
     * @param bool $isGlobal
     * @param int|null $estateId
     * @param string|null $title
     * @param string|null $description
     * @param string|null $group
     * @return AppSetting
     */
    public function put(string $key, $value, bool $isGlobal, ?int $estateId = null, ?string $title = null, ?string $description = null, ?string $group = null): AppSetting {
        if (empty($key)) {
            throw new Exception('A valid key must be passed to put config.');
        }

        if (! $isGlobal) {
            if ($estateId === null) {
                throw new Exception('estate_id is required when is_global is false.');
            }

            if (! Estate::where('id', $estateId)->exists()) {
                throw new Exception("Invalid estate id '{$estateId}' passed to put config.");
            }
        }

        return AppSetting::set($key, $value, $title, $description, $group, $isGlobal, $estateId);
    }

    /**
     * Resolve from app settings. When an estate id is given the estate scoped
     * value is preferred and falls back to the global setting.
     *
     * @param string $key
     * @param int|null $estateId
     * @return mixed
     */
    private function resolveFromAppSetting(string $key, ?int $estateId = null) {
        return AppSetting::get($key, null, $estateId);
    }

    /**
     * Resolve from the single-row settings table.
     *
     * @param string $key
     * @param int|null $estateId
     * @return mixed
     */
    private function resolveFromSetting(string $key, ?int $estateId = null) {
        $setting = Setting::where('id', 1)->first();

        if (! $setting || ! array_key_exists($key, $setting->getAttributes())) {
            return null;
        }

        return $setting->getAttribute($key);
    }

    /**
     * Resolve from the constants config file.
     *
     * @param string $key
     * @param int|null $estateId
     * @return mixed
     */
    private function resolveFromConfig(string $key, ?int $estateId = null) {
        $key = underscore_to_hyphen($key, $reverse);
        return config('constants.' . $key);
    }

    /**
     * Resolve from the environment. The key is uppercased and dashes are
     * replaced with underscores (e.g. foo-bar becomes FOO_BAR).
     *
     * @param string $key
     * @param int|null $estateId
     * @return mixed
     */
    private function resolveFromEnv(string $key, ?int $estateId = null) {
        return env(str_replace('-', '_', strtoupper($key)));
    }
}
