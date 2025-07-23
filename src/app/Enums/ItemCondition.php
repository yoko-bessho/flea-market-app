<?php

namespace App\Enums;

class ItemCondition
{
    public const GOOD = 'good';
    public const OK = 'ok';
    public const FAIR = 'fair';
    public const POOR = 'poor';

        public static function label(string $value): string
        {
            switch ($value) {
                case self::GOOD:
                    return '良好';
                case self::OK:
                    return '目立った傷や汚れなし';
                case self::FAIR:
                    return 'やや傷や汚れあり';
                case self::POOR:
                    return '状態が悪い';
            }
        }

        // conditionの選択肢用
        public static function options(): array
        {
            return [
                ['value' => self::GOOD, 'label' => self::label(self::GOOD)],
                ['value' => self::OK, 'label' => self::label(self::OK)],
                ['value' => self::FAIR, 'label' => self::label(self::FAIR)],
                ['value' => self::POOR, 'label' => self::label(self::POOR)],
            ];
        }
}

