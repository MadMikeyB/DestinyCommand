<?php

namespace App\Destiny;

use App\Destiny\Filters\InventoryFilter;

/**
 * Wraps a Bungie profile payload and exposes command-specific projections.
 */
class Profile
{
    /**
     * Build a new profile wrapper.
     */
    public function __construct(array|object $aProperties = [])
    {
        foreach ($aProperties as $strProperty => $oProperty) {
            $this->$strProperty = $oProperty;
        }
    }

    /**
     * Extract a character profile field for each returned character.
     */
    public function getCharacterProfileValue(object $oOptions): array
    {
        $aRes = [];
        if (isset($oOptions->field)) {
            foreach ($this->characters->data as $iCharacterId => $oCharacter) {
                $aRes[$iCharacterId][$oOptions->field] = new CharacterProfileValue($oOptions->field, $oCharacter->{$oOptions->field}, $oCharacter->classHash);
            }
        }

        return $aRes;
    }

    /**
     * Extract character progression data for each returned character.
     */
    public function getCharacterProgression(object $oOptions): array
    {
        $aRes = [];
        $iLatest = false;
        if ($oOptions->latest) {
            $iLatest = $this->getLatestCharacterId();
        }

        foreach ($this->characters->data as $iCharacterId => $oCharacter) {
            if ($iLatest && $iLatest !== $iCharacterId) {
                continue;
            }

            foreach ($oOptions->progressions as $iProgressionId) {
                if (isset($this->characterProgressions->data->{$iCharacterId}->progressions->{$iProgressionId})) {
                    $oProgression = $this->characterProgressions->data->{$iCharacterId}->progressions->{$iProgressionId};
                    $aRes[$iCharacterId][$iProgressionId] = new CharacterProgressionValue($iProgressionId, $oProgression->level, $oCharacter->classHash);
                }
            }
        }

        return $aRes;
    }

    /**
     * Extract equipment data for the selected characters.
     */
    public function getCharacterEquipment(object $oOptions): array
    {
        $aRes = [];
        $bPerks = $oOptions->perks ?? false;
        $iLatest = false;
        if ($oOptions->latest) {
            $iLatest = $this->getLatestCharacterId();
        }

        foreach ($this->characters->data as $iCharacterId => $oCharacter) {
            if ($iLatest && $iLatest !== $iCharacterId) {
                continue;
            }

            $oInventoryFilter = new InventoryFilter(
                $this->characterEquipment->data->{$iCharacterId}->items,
                $this->itemComponents->instances->data,
                $this->itemComponents->sockets->data
            );
            $aRes[$iCharacterId] = $oInventoryFilter->getItems($oOptions->field, $bPerks);
        }

        return $aRes;
    }

    /**
     * Get the most recently played character id.
     */
    public function getLatestCharacterId(): int|string|false
    {
        $dLastPlayed = false;
        $iLatestCharacterId = false;
        foreach ($this->characters->data as $iCharacterId => $oCharacter) {
            if ($dLastPlayed === false || $dLastPlayed < strtotime($oCharacter->dateLastPlayed)) {
                $dLastPlayed = strtotime($oCharacter->dateLastPlayed);
                $iLatestCharacterId = $iCharacterId;
            }
        }

        return $iLatestCharacterId;
    }
}
