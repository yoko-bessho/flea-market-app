<?php

namespace App\Enums;

class ItemCondition
{
    public const GOOD = 'good';
    public const OK = 'ok';
    public const FAIR = 'fair';
    public const POOR = 'poor';

        public static function values(): array
        {
            return [
                self::GOOD,
                self::OK,
                self::FAIR,
                self::POOR,
            ];
        }

        public static function labels(string $value): string
        {
            $labels = [
                self::GOOD => '良好',
                self::OK => '普通',
                self::FAIR => 'やや悪い',
                self::POOR => '悪い',
            ];

            return $labels[$value] ?? '';
        }


}

