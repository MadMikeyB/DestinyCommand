<?php

namespace App\Enums;

/**
 * Enumerates profile fields exposed by command output.
 */
enum CharacterProfileField: string
{
    case LIGHT = 'light';

    /**
     * Get the display title for the field.
     */
    public function title(): string
    {
        return match ($this) {
            self::LIGHT => 'Power level',
        };
    }
}
