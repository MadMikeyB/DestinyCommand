<?php

namespace App\Enums;

/**
 * Enumerates character progression-backed command actions.
 */
enum CharacterProgressionAction: string
{
    case CARD = 'card';

    /**
     * Determine whether the command should use only the latest character.
     */
    public function latest(): bool
    {
        return true;
    }

    /**
     * Get the progression hashes required by the command action.
     *
     * @return array<int>
     */
    public function progressions(): array
    {
        return match ($this) {
            self::CARD => [
                CharacterProgressionField::CURRENT_TRIALS_CARD_WINS->value,
                CharacterProgressionField::FLAWLESS->value,
            ],
        };
    }

    /**
     * Get the command-key to progression metadata mapping.
     */
    public static function mapping(): array
    {
        $mapping = [];

        foreach (self::cases() as $case) {
            $mapping[$case->value] = (object) [
                'latest' => $case->latest(),
                'progressions' => $case->progressions(),
            ];
        }

        return $mapping;
    }
}
