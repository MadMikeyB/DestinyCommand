<?php

namespace App\Destiny;

use App\Destiny\Filters\InventoryFilter;

class Profile
{
    public function __construct($aProperties = [])
    {
        foreach ($aProperties as $strProperty => $oProperty) {
            $this->$strProperty = $oProperty;
        }
    }

    public function getCharacterProfileValue($oOptions)
    {
        $aRes = [];
        if (isset($oOptions->field)) {
            foreach ($this->characters->data as $iCharacterId => $oCharacter) {
                $aRes[$iCharacterId][$oOptions->field] = new CharacterProfileValue($oOptions->field, $oCharacter->{$oOptions->field}, $oCharacter->classHash);
            }
        }

        return $aRes;
    }

    public function getCharacterProgression($oOptions)
    {
        $aRes = [];
        $iLatest = false;
        if ($oOptions->latest) {
            $iLatest = $this->getLatestCharacterId();
        }

        foreach ($this->characters->data as $iCharacterId => $oCharacter) {
            if ($iLatest && $iLatest != $iCharacterId) {
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

    public function getCharacterEquipment($oOptions)
    {
        $aRes = [];
        $bPerks = $oOptions->perks ?? false;
        $iLatest = false;
        if ($oOptions->latest) {
            $iLatest = $this->getLatestCharacterId();
        }

        foreach ($this->characters->data as $iCharacterId => $oCharacter) {
            if ($iLatest && $iLatest != $iCharacterId) {
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

    public function getLatestCharacterId()
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
