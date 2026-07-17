<?php

namespace App\Command;

use App\Enums\CommandEndpoint;
use App\Enums\CommandProvider;

/**
 * Resolves a raw action token into a normalized command definition.
 */
class Action
{
    public string $key;

    public string $provider;

    public ?string $title = null;

    public ?string $text = null;

    public ?string $endpoint = null;

    public ?string $filter = null;

    public bool $noUser = false;

    public ?object $options = null;

    /**
     * Build a normalized action definition from a raw action token.
     */
    public function __construct(string $strAction)
    {
        $aAction = false;
        $strAction = $this->getAlias($strAction);
        foreach ($this->actionResolvers() as $strFunction) {
            $aAction = $this->{$strFunction}($strAction);
            if ($aAction !== false) {
                break;
            }
        }

        if ($aAction === false || is_null($aAction)) {
            $aAction = $this->isTextCommand('default_info');
        }

        $this->hydrate($aAction);
    }

    /**
     * Get the ordered list of action resolvers.
     */
    private function actionResolvers(): array
    {
        return ['isTextCommand', 'isTrialsReportCommand', 'isVendorCommand', 'isGearCommand', 'isCharacterProfileCommand', 'isCharacterProgressionCommand', 'isStatCommand'];
    }

    /**
     * Hydrate the action object from a resolved definition array.
     */
    private function hydrate(array $aAction): void
    {
        foreach ($aAction as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Resolve vendor commands.
     */
    private function isVendorCommand(string $strAction): array|false
    {
        $aVendorActions = CommandCatalog::vendorActions();

        if (isset($aVendorActions[$strAction])) {
            return [
                'key' => $strAction,
                'title' => $strAction,
                'provider' => CommandProvider::BUNGIE->value,
                'endpoint' => CommandEndpoint::VENDOR->value,
                'filter' => 'getSales',
                'noUser' => true,
                'options' => (object) [
                    'params' => [
                        'components' => [400, 402],
                    ],
                    'hash' => $aVendorActions[$strAction],
                ],
            ];
        }

        return false;
    }

    /**
     * Resolve character progression commands.
     */
    private function isCharacterProgressionCommand(string $strAction): array|false
    {
        $aProgressions = CommandCatalog::characterProgressions();

        if (isset($aProgressions[$strAction])) {
            return [
                'key' => $strAction,
                'title' => $strAction,
                'provider' => CommandProvider::BUNGIE->value,
                'endpoint' => CommandEndpoint::PROFILE->value,
                'filter' => 'getCharacterProgression',
                'options' => (object) [
                    'params' => [
                        'components' => [202],
                    ],
                    'latest' => $aProgressions[$strAction]->latest,
                    'progressions' => $aProgressions[$strAction]->progressions,
                ],
            ];
        }

        return false;
    }

    /**
     * Resolve character profile value commands.
     */
    private function isCharacterProfileCommand(string $strAction): array|false
    {
        if (isset($strAction[0]) && $strAction[0] === 'c' && strlen($strAction) > 2) {
            $strAction = substr($strAction, 1);
        }

        $aCharacterProfileActions = CommandCatalog::characterProfileActions();

        if (isset($aCharacterProfileActions[$strAction])) {
            return [
                'key' => $strAction,
                'title' => '',
                'provider' => CommandProvider::BUNGIE->value,
                'endpoint' => CommandEndpoint::PROFILE->value,
                'filter' => 'getCharacterProfileValue',
                'options' => (object) [
                    'field' => $aCharacterProfileActions[$strAction],
                ],
            ];
        }

        return false;
    }

    /**
     * Resolve Trials Report commands.
     */
    private function isTrialsReportCommand(string $strAction): array|false
    {
        $aTrialsReportActions = CommandCatalog::trialsReportActions();

        if (isset($aTrialsReportActions[$strAction])) {
            return [
                'key' => 'TrialsTeam',
                'title' => 'TrialsTeam',
                'provider' => CommandProvider::TRIALS_REPORT->value,
                'endpoint' => $aTrialsReportActions[$strAction],
                'filter' => 'getFireteamStats',
                'options' => (object) [
                    'team' => (strpos($strAction, 'team') !== false ? true : false),
                ],
            ];
        }

        return false;
    }

    /**
     * Resolve equipment and loadout commands.
     */
    private function isGearCommand(string $strAction): array|false
    {
        $aItemActions = CommandCatalog::gearActions();

        foreach ($aItemActions as $xKey => $xItemAction) {
            $c = false;
            if (is_array($xItemAction) && $xKey == $strAction) {
                $strTitle = $xKey;
                $bPerks = false;
                $xField = $xItemAction;
                $c = true;
            } elseif ($strAction == $xItemAction) {
                $bPerks = true;
                $strTitle = $strAction;
                $xField = [$strAction];
                $c = true;
            }

            if ($c) {
                return [
                    'key' => $strTitle,
                    'title' => $strTitle,
                    'provider' => CommandProvider::BUNGIE->value,
                    'endpoint' => CommandEndpoint::PROFILE->value,
                    'filter' => 'getCharacterEquipment',
                    'options' => (object) [
                        'perks' => $bPerks,
                        'params' => [
                            'components' => [205, 305, 300],
                        ],
                        'latest' => true,
                        'field' => $xField,
                    ],
                ];
            }
        }

        return false;
    }

    /**
     * Resolve stat and medal commands.
     */
    private function isStatCommand(string $strAction): array|false
    {
        $c = false;
        $aStatActions = CommandCatalog::statActions();
        $aStatMedals = CommandCatalog::statMedals();
        $aPlaylists = CommandCatalog::playlists();

        $iModes = 5; // default PvP.
        $bPGA = false; // default false.
        $bSeperate = false; // default false.

        $aGambitStats = CommandCatalog::gambitStats();

        if (in_array($strAction, $aGambitStats, true) || in_array(substr($strAction, 1), $aGambitStats, true)) {
            $iModes = 63; // These stats only will work for Gambit
        } else {
            foreach ($aPlaylists as $strPlaylist => $iPlaylistModes) {
                if (strpos($strAction, $strPlaylist) !== false) {
                    $iModes = $iPlaylistModes;
                    $strAction = str_replace($strPlaylist, '', $strAction);

                    if ($strAction === '' || $strAction === 'c') {
                        $c = true;
                        $xField = ['killsDeathsRatio', 'winLossRatio', 'activitiesWon'];
                        $strTitle = 'summary';
                        break;
                    }
                }
            }
        }

        if (isset($strAction[0]) && $strAction[0] === 'c' && strlen($strAction) > 2) {
            $bSeperate = true;
            $strAction = substr($strAction, 1);
        }

        if (strlen($strAction) > 3 && substr($strAction, -3) === 'pga') {
            $bPGA = true;
            $strAction = substr($strAction, 0, -3);
        }

        // check alias again, since we removed the pga and c part
        $strAction = $this->getAlias($strAction);
        $bMedal = false;
        if ($strAction !== '' && (isset($aStatActions[$strAction]) || isset($aStatMedals[$strAction]))) {
            if (isset($aStatActions[$strAction])) {
                $xStat = $aStatActions[$strAction];
            } else {
                $xStat = $aStatMedals[$strAction];
                $bMedal = true;
            }

            if (is_array($xStat)) {
                $strTitle = $strAction;
                $xField = $xStat;
                $c = true;
            } else {
                $strTitle = $strAction;
                $xField = [$xStat];
                $c = true;
            }
        }

        if ($c) {
            return [
                'key' => $strTitle,
                'title' => '',
                'provider' => CommandProvider::BUNGIE->value,
                'endpoint' => CommandEndpoint::STATS->value,
                'filter' => 'getStats',
                'options' => (object) [
                    'field' => $xField,
                    'modes' => $iModes,
                    'groups' => ($bMedal ? 'Medals' : 'General'),
                    'seperate' => $bSeperate,
                    'pga' => $bPGA,
                ],
            ];
        }

        return false;
    }

    /**
     * Resolve plain-text commands.
     */
    private function isTextCommand(string $strAction): array|false
    {
        $a = CommandCatalog::textCommands();
        $a['ratemybutt'] = $this->rateMyButt();

        if (isset($a[$strAction])) {
            return [
                'key' => $strAction,
                'text' => $a[$strAction],
                'provider' => CommandProvider::PLAIN_TEXT->value,
                'noUser' => true,
            ];
        }

        return false;
    }

    /**
     * Generate the legacy random butt rating text response.
     */
    private function rateMyButt(): string
    {
        $x = rand(1, 10);
        $i = 10;
        if ($x === 5) {
            $a = rand(1, 4);
            if ($a === 1) {
                $i = 7;
            } // 5/7 ratings Kappa
        }

        return 'butt rated: '.$x.'/'.$i;
    }

    /**
     * Resolve an action alias to its canonical command key.
     */
    private function getAlias(string $strAction): string
    {
        $a = CommandCatalog::actionAliases();

        return isset($a[$strAction]) ? $a[$strAction] : $strAction;
    }
}
