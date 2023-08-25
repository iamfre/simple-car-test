<?php

namespace App\Enum;

use function Symfony\Component\String\s;

enum CarAvailableSortingColums: string
{
    case MODEL = 'model';
    case BRAND_ID = 'brand_id';
    case PRICE = 'price';
    case SAIL_PRICE = 'sail_price';
    case YEAR = 'year';
    case UPDATED_AT = 'updated_at';

    public static function all(): array
    {
        return [self::MODEL->value, self::BRAND_ID->value, self::PRICE->value, self::SAIL_PRICE->value,
            self::YEAR->value, self::UPDATED_AT->value];
    }

    public static function fromRequest($status): ?string
    {
        return match ($status) {
            'model' => self::MODEL->value,
            'brand_id' => self::BRAND_ID->value,
            'price' => self::PRICE->value,
            'sail_price' => self::SAIL_PRICE->value,
            'year' => self::YEAR->value,
            'updated_at' => self::UPDATED_AT->value,
            default => null
        };
    }
}