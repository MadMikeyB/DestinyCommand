<?php

namespace App\Enums;

/**
 * Enumerates vendor-backed command actions.
 */
enum VendorAction: string
{
    case XUR = 'xur';

    /**
     * Get the vendor hash for the command action.
     */
    public function hash(): int
    {
        return match ($this) {
            self::XUR => 2190858386,
        };
    }

    /**
     * Get the command-key to vendor-hash mapping.
     */
    public static function mapping(): array
    {
        $mapping = [];

        foreach (self::cases() as $case) {
            $mapping[$case->value] = $case->hash();
        }

        return $mapping;
    }
}
