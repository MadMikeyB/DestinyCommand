<?php

namespace App\Enums;

/**
 * Enumerates progression hashes exposed by command output.
 */
enum CharacterProgressionField: int
{
    case CURRENT_TRIALS_CARD_WINS = 1062449239;
    case FLAWLESS = 2093709363;

    /**
     * Get the display title for the progression field.
     */
    public function title(): string
    {
        return match ($this) {
            self::CURRENT_TRIALS_CARD_WINS => 'Current Trials card: Wins',
            self::FLAWLESS => 'Flawless',
        };
    }
}
