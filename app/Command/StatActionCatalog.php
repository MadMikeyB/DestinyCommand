<?php

namespace App\Command;

/**
 * Provides stat command metadata and classifications.
 */
class StatActionCatalog
{
    /**
     * Get stat command mappings.
     */
    public static function mapping(): array
    {
        return [
            'games' => 'activitiesEntered',
            'wins' => 'activitiesWon',
            'assists' => 'assists',
            'tdd' => 'totalDeathDistance',
            'avgdd' => 'averageDeathDistance',
            'tkd' => 'totalKillDistance',
            'avgkd' => 'averageKillDistance',
            'time' => 'secondsPlayed',
            'deaths' => 'deaths',
            'kills' => 'kills',
            'avgls' => 'averageLifespan',
            'score' => 'score',
            'avgspk' => 'averageScorePerKill',
            'avgspl' => 'averageScorePerLife',
            'mk' => 'bestSingleGameKills',
            'bestscore' => 'bestSingleGameScore',
            'kd' => 'killsDeathsRatio',
            'kda' => 'killsDeathsAssists',
            'pkills' => 'precisionKills',
            'res' => 'resurrectionsPerformed',
            'resres' => 'resurrectionsReceived',
            'suicides' => 'suicides',
            'fusion' => 'weaponKillsFusionRifle',
            'handcannon' => 'weaponKillsHandCannon',
            'auto' => 'weaponKillsAutoRifle',
            'machinegun' => 'weaponKillsMachinegun',
            'melee' => 'weaponKillsMelee',
            'pulse' => 'weaponKillsPulseRifle',
            'rocket' => 'weaponKillsRocketLauncher',
            'scout' => 'weaponKillsScoutRifle',
            'shotgun' => 'weaponKillsShotgun',
            'sniper' => 'weaponKillsSniper',
            'smg' => 'weaponKillsSubmachinegun',
            'relic' => 'weaponKillsRelic',
            'sidearm' => 'weaponKillsSideArm',
            'sword' => 'weaponKillsSword',
            'akills' => 'weaponKillsAbility',
            'grenade' => 'weaponKillsGrenade',
            'grenadelauncher' => 'weaponKillsGrenadeLauncher',
            'bow' => 'weaponKillsBow',
            'bestwep' => 'weaponBestType',
            'wl' => 'winLossRatio',
            'lks' => 'longestKillSpree',
            'lsl' => 'longestSingleLife',
            'mpk' => 'mostPrecisionKills',
            'orbs' => 'orbsDropped',
            'orbsg' => 'orbsGathered',
            'cr' => 'combatRating',
            'fastest' => 'fastestCompletionMs',
            'lkd' => 'longestKillDistance',
            'invasions' => 'invasions',
            'invasionkills' => 'invasionKills',
            'invaderkills' => 'invaderKills',
            'invaderdeaths' => 'invaderDeaths',
            'primevalkills' => 'primevalKills',
            'blockerkills' => 'blockerKills',
            'mobkills' => 'mobKills',
            'highvaluekills' => 'highValueKills',
            'motespickedup' => 'motesPickedUp',
            'motesdeposited' => 'motesDeposited',
            'motesdenied' => 'motesDenied',
            'motesdegraded' => 'motesDegraded',
            'moteslost' => 'motesLost',
            'bankoverage' => 'bankOverage',
            'smallblockers' => 'smallBlockersSent',
            'mediumblockers' => 'mediumBlockersSent',
            'largeblockers' => 'largeBlockersSent',
            'primevaldamage' => 'primevalDamage',
            'primevalhealing' => 'primevalHealing',
            'gbroundsplayed' => 'roundsPlayed',
            'gbroundswon' => 'roundsWon',
        ];
    }

    /**
     * Get stat command keys that should resolve against Gambit activity modes.
     */
    public static function gambitKeys(): array
    {
        return [
            'invasions', 'invasionkills', 'invaderkills', 'invaderdeaths', 'primevalkills', 'blockerkills',
            'mobkills', 'highvaluekills', 'motespickedup', 'motesdeposited', 'motesdenied', 'motesdegraded',
            'moteslost', 'bankoverage', 'smallblockers', 'mediumblockers', 'largeblockers', 'primevaldamage',
            'primevalhealing', 'gbroundsplayed', 'gbroundswon',
        ];
    }
}
