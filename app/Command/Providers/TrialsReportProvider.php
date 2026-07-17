<?php

namespace App\Command\Providers;

use App\Destiny\Filters\TrialsReportFilter;
use App\Services\TrialsReportService;

/**
 * Executes Trials Report-backed command actions.
 */
class TrialsReportProvider
{
    /**
     * Create a new Trials Report provider instance.
     */
    public function __construct(
        private ?TrialsReportService $trialsReportService = null,
    ) {
        $this->trialsReportService ??= new TrialsReportService;
    }

    /**
     * Prepare or fetch Trials Report data for a command action.
     */
    public function fetch(object $oAction, array $aParameters, bool $bPrepare = false): mixed
    {
        switch ($oAction->endpoint) {
            case 'getFireteam':

                if ($bPrepare === true) {
                    foreach ($aParameters['players'] as $oPlayer) {
                        $this->trialsReportService->queueFireteam($oPlayer->membershipId, $oPlayer->membershipType);
                    }
                } else {
                    $aTRProfiles = $this->trialsReportService->responsesFor('getFireteam');
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
