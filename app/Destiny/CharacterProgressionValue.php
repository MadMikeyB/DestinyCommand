<?php

namespace App\Destiny;

use App\Enums\CharacterProgressionField;

/**
 * Represents a formatted per-character progression value.
 */
class CharacterProgressionValue
{
    public mixed $displayValue;

    public string $title;

    public int|string $classHash;

    /**
     * Build a character progression value from a raw Bungie field.
     */
    public function __construct(int|string $strKey, mixed $xValue, int|string $strClassHash)
    {
        $this->displayValue = $xValue;
        $this->title = $this->getTitle($strKey);
        $this->classHash = $strClassHash;
    }

    /**
     * Resolve a display title for the given progression field.
     */
    private function getTitle(int|string $strKey): string
    {
        if (! is_int($strKey)) {
            return '';
        }

        return CharacterProgressionField::tryFrom($strKey)?->title() ?? '';
    }
}
