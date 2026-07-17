<?php

namespace App\Destiny\Filters;

use App\Destiny\TrialsReportFireteamReport;

/**
 * Filters Trials Report payloads into command-ready summaries.
 */
class TrialsReportFilter
{
    /**
     * Build a filter from a raw Trials Report response payload.
     */
    public function __construct(mixed $oData)
    {
        if (! empty($oData)) {
            foreach ($oData as $strKey => $xValue) {
                $this->{$strKey} = $xValue;
            }
        }
    }

    /**
     * Extract the fireteam summary data used by command responses.
     */
    public function getFireteamStats(object|array $oOptions = []): array
    {
        $teamOption = is_object($oOptions) ? $oOptions->team ?? null : null;

        if (isset($this->results) && ! empty($this->results)) {
            $aTeam = [];
            foreach ($this->results as $oPlayer) {
                if (isset($oPlayer->current)) {
                    $aTeam[] = new TrialsReportFireteamReport((object) [
                        'displayName' => $oPlayer->displayName,
                        'kills' => $oPlayer->current->kills,
                        'deaths' => $oPlayer->current->deaths,
                        'assists' => $oPlayer->current->assists,
                        'games' => $oPlayer->current->matches,
                        'winp' => $oPlayer->current->losses === 0 ? 100 : (($oPlayer->current->matches - $oPlayer->current->losses) > 0 ? number_format(((($oPlayer->current->matches - $oPlayer->current->losses) / $oPlayer->current->matches) * 100), 2, '.', ',') : 0),
                        'kd' => $oPlayer->current->deaths > 0 ? number_format(($oPlayer->current->kills / $oPlayer->current->deaths), 2, '.', ',') : $oPlayer->current->kills,
                        'kda' => $oPlayer->current->deaths > 0 ? number_format((($oPlayer->current->kills + $oPlayer->current->assists) / $oPlayer->current->deaths), 2, '.', ',') : $oPlayer->current->kills,
                        'flawless' => $oPlayer->current->flawless,
                    ]);
                }

                if ($teamOption === false) {
                    break;
                }
            }

            return $aTeam;
        }

        return [false];
    }
}
