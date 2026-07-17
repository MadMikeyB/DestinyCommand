<?php

namespace App\Destiny;

use App\Enums\CharacterProfileField;

/**
 * Represents a formatted per-character profile field value.
 */
class CharacterProfileValue
{
    public mixed $displayValue;

    public string $title;

    public int|string $classHash;

    /**
     * Build a character profile value from a raw Bungie field.
     */
    public function __construct(string $strKey, mixed $xValue, int|string $strClassHash)
    {
        $this->displayValue = $xValue;
        $this->title = $this->getTitle($strKey);
        $this->classHash = $strClassHash;
    }

    /**
     * Resolve a display title for the given profile field.
     */
    private function getTitle(string $strKey): string
    {
        return CharacterProfileField::tryFrom($strKey)?->title() ?? '';
    }
}
