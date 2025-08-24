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

        public static function allLabels(): array
        {
            return [
                self::GOOD => '良好',
                self::OK => '目立った傷や汚れなし',
                self::FAIR => 'やや傷や汚れあり',
                self::POOR => '状態が悪い',
            ];
        }

        public static function label(?string $value): string
        {
            return self::alllabels()[$value] ?? '';
        }
}

