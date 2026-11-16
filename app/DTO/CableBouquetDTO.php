<?php

namespace App\DTO;

class CableBouquetDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $description,
        public readonly string $price,
    ) {}

    public static function fromVtuPackage(array $package): self
    {
        return new self(
            code: (string) ($package['variation_code'] ?? $package['code'] ?? ''),
            description: (string) ($package['name'] ?? $package['description'] ?? ''),
            price: number_format((float) ($package['amount'] ?? $package['price'] ?? 0), 2, '.', ''),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
