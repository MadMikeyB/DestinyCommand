<?php

namespace App\Destiny;

/**
 * Represents a formatted Trials Report fireteam member summary.
 */
class TrialsReportFireteamReport
{
    public string $displayName;

    public int|float $kills;

    public int|float $deaths;

    public int|float $assists;

    public int|float|string $winp;

    public int|float|string $kd;

    public int|float|string $kda;

    public int|float $games;

    public int|float $flawless;

    /**
     * Build a report item from the upstream Trials Report payload.
     */
    public function __construct(object $oStatReport)
    {
        $this->displayName = $oStatReport->displayName;
        $this->kills = $oStatReport->kills;
        $this->deaths = $oStatReport->deaths;
        $this->assists = $oStatReport->assists;
        $this->winp = $oStatReport->winp;
        $this->kd = $oStatReport->kd;
        $this->kda = $oStatReport->kda;
        $this->games = $oStatReport->games;
        $this->flawless = $oStatReport->flawless;
    }
}
