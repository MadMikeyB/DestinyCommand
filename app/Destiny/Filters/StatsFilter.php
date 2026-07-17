<?php

namespace App\Destiny\Filters;

use App\Destiny\Stat;

/**
 * Filters Bungie historical stats payloads into command-ready values.
 */
class StatsFilter
{
    private $playlist;

    private $stats = [];

    /**
     * Build a stats filter from a raw historical stats response.
     */
    public function __construct(object $oStats)
    {
        $oPlaylist = null;
        $oStatsObject = null;

        foreach ($oStats as $strPlaylist => $oPlaylist) {
            break;
        }

        if (empty((array) $oPlaylist)) {
            return;
        }

        $this->playlist = $strPlaylist;

        foreach ($oPlaylist as $oStatsObject) {
            break;
        }

        if (! is_object($oStatsObject)) {
            return;
        }

        foreach ($oStatsObject as $strKey => $oStat) {
            $this->stats[$strKey] = $oStat;
        }
    }

    /**
     * Resolve one or more requested stat values.
     */
    public function getStats(array|string $aSearchItems): array|Stat|false
    {
        $bArray = true;
        if (! is_array($aSearchItems)) {
            $aSearchItems = [$aSearchItems];
            $bArray = false;
        }

        $a = [];
        foreach ($aSearchItems as $strSearchItem) {
            $oStat = false;
            if (isset($this->stats[$strSearchItem])) {
                $oStat = new Stat($this->stats[$strSearchItem], $this->playlist);
            }
            $a[$strSearchItem] = $oStat;
        }

        return $bArray === false ? $a[$strSearchItem] : $a;
    }
}
