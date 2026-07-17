<?php

namespace App\Destiny\Filters;

use App\Destiny\Stat;

class StatsFilter
{
    private $playlist;

    private $timePeriod;

    private $stats = [];

    public function __construct($oStats)
    {
        foreach ($oStats as $strPlaylist => $oPlaylist) {
            break;
        }

        if (empty((array) $oPlaylist)) {
            return false;
        }
        $this->playlist = $strPlaylist;

        foreach ($oPlaylist as $strTimePeriod => $oStatsObject);
        $this->timePeriod = $strTimePeriod;

        foreach ($oStatsObject as $strKey => $oStat) {
            $this->stats[$strKey] = $oStat;
        }
    }

    public function getStats($aSearchItems)
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
