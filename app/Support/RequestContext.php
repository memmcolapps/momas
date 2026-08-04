<?php

namespace App\Support;

class RequestContext
{
    protected array $data = [];

    public function put(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function merge(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
}
