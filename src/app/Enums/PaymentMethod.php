<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case Convenience = 'convenience';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
