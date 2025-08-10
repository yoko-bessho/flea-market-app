<?php

namespace App\Enums;

class PaymentMethod
{
    public const CARD = 'card';
    public const CONVENIENCE = 'convenience';

    public static function values(): array
    {
        return [
            self::CARD,
            self::CONVENIENCE,
        ];
    }

    public static function labels(): array
    {
        return [
            self::CARD => 'カード支払い',
            self::CONVENIENCE => 'コンビニ払い'
        ];
    }
}
