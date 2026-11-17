<?php

namespace App\Enums;

enum AlertMode: string
{
    case PROMOTION = 'promotion';
    case CELEBRATION = 'celebration';
    case NOTICE = 'notice';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $mode) => [$mode->value => $mode->label()])
            ->all();
    }
}
