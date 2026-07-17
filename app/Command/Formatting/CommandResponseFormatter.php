<?php

namespace App\Command\Formatting;

use App\Command\CommandContext;
use App\Destiny\CharacterProfileValue;
use App\Destiny\CharacterProgressionValue;
use App\Destiny\EquipmentItem;
use App\Destiny\Stat;
use App\Destiny\TrialsReportFireteamReport;

/**
 * Formats command execution results into legacy chat-friendly output.
 */
class CommandResponseFormatter
{
    private const CLASSES = [
        671679327 => 'Hunter',
        2271682572 => 'Warlock',
        3655393761 => 'Titan',
    ];

    /**
     * Format execution results for the target chat platform.
     */
    public function format(CommandContext $command, array $result, array $prep = [], bool $displayUsername = true, bool $displayGamertag = true): string
    {
        if ($command->platform === 'discord' && isset($command->responseUser)) {
            $command->responseUser = $this->formatDiscord($command->responseUser);
        }

        $response = $displayUsername ? '@'.$command->responseUser.': ' : '';

        if ($command->platform === 'discord') {
            $response .= '```';
        }

        if (isset($result['players']) && ! empty($result['players'])) {
            $response .= $this->formatPlayerResults($result, $prep, $displayGamertag);
        } elseif (! isset($result['response']['text']) && ! empty($result['response']) && is_array($result['response'])) {
            $response .= $this->formatInventoryResults($result['response']);
        }

        if (isset($result['response']['text'])) {
            $response .= ' '.implode(',', $result['response']['text']);
        }

        $response .= '.';

        if ($command->platform === 'discord') {
            $response .= '```';
        }

        return $response;
    }

    /**
     * Format all per-player response data into a single output string.
     */
    private function formatPlayerResults(array $result, array $prep, bool $displayGamertag): string
    {
        $response = '';

        foreach ($result['players'] as $player) {
            $resultKey = $player->membershipType.'-'.$player->membershipId;

            if (! isset($result['response'][$resultKey])) {
                continue;
            }

            $playlistIntro = false;
            $response .= $displayGamertag ? $this->formatNoBnet($player->displayName).': ' : '';
            if (count($result['response'][$resultKey]) > 1) {
                $response .= '[';
            }

            $found = false;
            foreach ($result['response'][$resultKey] as $characterId => $character) {
                $characterResponse = false;

                foreach ($character as $item) {
                    [$characterResponse, $found, $stat] = $this->appendCharacterItem(
                        $characterResponse,
                        $item,
                        $found,
                        $result,
                        $resultKey,
                    );

                    if ($characterResponse === '__SKIP__') {
                        $characterResponse = false;

                        continue 2;
                    }
                }

                if ($characterResponse !== false) {
                    if (isset($stat) && $playlistIntro === false) {
                        $response .= '['.$this->normalizePlaylistName($stat->playlist).'] ';
                        $playlistIntro = true;
                    }

                    if ($characterId !== 0 && isset($prep[$characterId])) {
                        $response .= self::CLASSES[$prep[$characterId]].': ';
                    }

                    $response .= $characterResponse;
                }
            }

            $response = ($found === true ? substr($response, 0, -2) : $response)
                .(count($result['response'][$resultKey]) > 1 ? ']' : '').', ';
            $response = substr($response, 0, -2).', ';
        }

        return substr($response, 0, -2);
    }

    /**
     * Append a single result item to the current character response.
     */
    private function appendCharacterItem(string|bool $characterResponse, mixed $item, bool $found, array $result, string $resultKey): array
    {
        $stat = null;

        switch (true) {
            case $item instanceof EquipmentItem:
                $characterResponse .= $item->name;
                if (isset($item->light) && $item->light > 50) {
                    $characterResponse .= ' ['.$item->light.']';
                }
                if (isset($item->perks) && ! empty($item->perks)) {
                    $characterResponse .= ' ['.implode(', ', $item->perks).']';
                }
                $characterResponse .= ', ';
                $found = true;
                break;

            case $item instanceof Stat:
                $stat = $item;
                $characterResponse .= $item->title.': '.$item->displayValue.', ';
                $found = true;
                break;

            case $item instanceof CharacterProfileValue:
                $characterResponse .= self::CLASSES[$item->classHash].': '.$item->title.': '.$item->displayValue.', ';
                $found = true;
                break;

            case $item instanceof CharacterProgressionValue:
                if (count($result['response'][$resultKey]) > 1) {
                    $characterResponse .= self::CLASSES[$item->classHash].': ';
                }

                if ($item->title === 'Flawless') {
                    if ($item->displayValue === 0 || $item->displayValue === '0') {
                        return ['__SKIP__', $found, $stat];
                    }

                    $item->displayValue = '❌';
                }

                $characterResponse .= $item->title.': '.$item->displayValue.', ';
                $found = true;
                break;

            case $item instanceof TrialsReportFireteamReport:
                $characterResponse .= '['.$this->formatNoBnet($item->displayName)
                    .': Games: '.$item->games
                    .($item->games > 0 ? ' (W'.$item->winp.'%)' : '')
                    .' | KD: '.$item->kd
                    .' | KA/D: '.$item->kda
                    .($item->flawless > 0 ? ' | Flawless: '.$item->flawless : '')
                    .'], ';
                $found = true;
                break;

            default:
                $found = true;
                $characterResponse .= 'No stats found, ';
        }

        return [$characterResponse, $found, $stat];
    }

    /**
     * Format inventory-style responses such as Xur or loadout items.
     */
    private function formatInventoryResults(array $responseParts): string
    {
        $response = '';

        if (isset($responseParts['textStart'])) {
            $response .= $responseParts['textStart'];
        }

        $items = 0;
        $limitReached = false;

        foreach ($responseParts as $item) {
            if (! $item instanceof EquipmentItem) {
                continue;
            }

            $items++;
            if ($items > 9) {
                if ($limitReached === false) {
                    $response .= '[Plus more..]  ';
                }

                $limitReached = true;
                break;
            }

            $response .= $item->name;
            if (isset($item->light) && $item->light > 50) {
                $response .= ' ['.$item->light.']';
            }
            if (isset($item->perks) && ! empty($item->perks)) {
                $response .= ' ['.implode(', ', $item->perks).']';
            }

            if (isset($item->costs) && ! empty($item->costs)) {
                $response .= ' [';
                foreach ($item->costs as $cost) {
                    $response .= $cost->quantity.', ';
                }
                $response = substr($response, 0, -2).']';
            }

            $response .= ', ';
        }

        $response = substr($response, 0, -2);

        if (isset($responseParts['textEnd'])) {
            $response .= ' ['.$responseParts['textEnd'].']';
        }

        return $response;
    }

    /**
     * Normalize playlist labels for chat output.
     */
    private function normalizePlaylistName(string $playlist): string
    {
        if (substr($playlist, 0, 3) === 'all') {
            $playlist = substr($playlist, 3);
        } elseif ($playlist === 'pvecomp_gambit') {
            $playlist = 'Gambit';
        } elseif ($playlist === 'pvecomp_mamba') {
            $playlist = 'Gambit Prime';
        } elseif ($playlist === 'trials_of_osiris') {
            $playlist = 'Trials';
        }

        return ucfirst($playlist);
    }

    /**
     * Escape Discord formatting characters in usernames.
     */
    private function formatDiscord(string $name): string
    {
        return str_replace(['_', '*', '+'], ['\_', '\*', ' '], $name);
    }

    /**
     * Strip the Bungie name code from a display name.
     */
    private function formatNoBnet(string $gamertag): string
    {
        $parts = explode('#', $gamertag);

        if (count($parts) > 1) {
            $gamertag = str_replace('#'.end($parts), '', $gamertag);
        }

        return $gamertag;
    }
}
