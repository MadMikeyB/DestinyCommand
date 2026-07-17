<?php

namespace App\Enums;

/**
 * Enumerates character profile-backed command actions.
 */
enum CharacterProfileAction: string
{
    case POWER_LEVEL = 'powerlevel';

    /**
     * Get the profile field exposed by the command action.
     */
    public function field(): string
    {
        return match ($this) {
            self::POWER_LEVEL => CharacterProfileField::LIGHT->value,
        };
    }

    /**
     * Get the command-key to profile-field mapping.
     */
    public static function mapping(): array
    {
        $mapping = [];

        foreach (self::cases() as $case) {
            $mapping[$case->value] = $case->field();
        }

        return $mapping;
    }
}
