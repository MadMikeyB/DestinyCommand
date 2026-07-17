<?php

namespace App\Providers;

use App\Destiny\Filters\TrialsReportFilter;
use App\Destiny\TrialsReportClient;

class TrialsReportProvider
{
    public function __construct()
    {
        $this->tr = new TrialsReportClient;
    }

    public function fetch($oAction, $aParameters, $bPrepare = false)
    {
        switch ($oAction->endpoint) {
            case 'getFireteam':

                if ($bPrepare === true) {
                    foreach ($aParameters['players'] as $oPlayer) {
                        $this->tr->getFireteam($oPlayer->membershipId, $oPlayer->membershipType);
                    }
                } else {
                    $aTRProfiles = $this->tr->get('getFireteam');
                    foreach ($aTRProfiles as $x => $aTRProfile) {
                        $oFilter = new TrialsReportFilter($aTRProfile);
                        $aProfiles[$x][0] = $oFilter->{$oAction->filter}($oAction->options ?? []);
                    }

                    return $aProfiles;
                }
                break;
        }
    }
}
