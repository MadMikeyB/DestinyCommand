<?php

namespace App\Command\Resolution;

use App\Command\CommandContext;
use App\Destiny\DestinyClient;
use App\Models\Destiny\BungieNetAccount;
use App\Models\Destiny\DestinyBungiePlayer;
use App\Models\Destiny\DestinyPlayer;
use Exception;

/**
 * Resolves command-supplied names into Bungie and Destiny identities.
 */
class CommandPlayerResolver
{
    /**
     * Resolve Bungie.net accounts for the supplied query data.
     */
    public function getAccounts(CommandContext $command, string|false $userSearch = false): array
    {
        $accounts = [];
        $search = false;
        $bungie = new DestinyClient;

        if ($userSearch === false) {
            if (! empty($command->query->gamertags)) {
                foreach ($command->query->gamertags as $index => $user) {
                    if (trim($user) === '') {
                        unset($command->query->gamertags[$index]);

                        continue;
                    }

                    $bungieNetAccount = BungieNetAccount::where([['uniqueName', '=', $user]])->first();
                    if ($bungieNetAccount) {
                        $accounts[$user] = $bungieNetAccount;
                        unset($command->query->gamertags[$index]);

                        continue;
                    }

                    $search = true;
                    $bungie->searchUsers($user);
                }
            }
        } else {
            $bungieNetAccount = BungieNetAccount::where([['uniqueName', '=', $userSearch]])->first();
            if ($bungieNetAccount) {
                $accounts[$userSearch] = $bungieNetAccount;
            } else {
                $search = true;
                $bungie->searchUsers($userSearch);
            }
        }

        if ($search) {
            $accountResults = $bungie->get('searchUsers');
            foreach ($accountResults as $user => $foundAccounts) {
                if (empty($foundAccounts)) {
                    throw new Exception('Account '.$user.' not found');
                }

                foreach ($foundAccounts as $foundAccount) {
                    if (strtolower($foundAccount->uniqueName) === strtolower($user)) {
                        $bungieNetAccount = BungieNetAccount::where([['membershipId', '=', $foundAccount->membershipId]])->first();
                        if ($bungieNetAccount) {
                            if ($bungieNetAccount->displayName !== $foundAccount->displayName || $bungieNetAccount->uniqueName !== $foundAccount->uniqueName) {
                                $bungieNetAccount->displayName = $foundAccount->displayName;
                                $bungieNetAccount->uniqueName = $foundAccount->uniqueName;
                                $bungieNetAccount->save();
                            }
                        } else {
                            $bungieNetAccount = BungieNetAccount::create([
                                'membershipId' => $foundAccount->membershipId,
                                'displayName' => $foundAccount->displayName,
                                'uniqueName' => $foundAccount->uniqueName,
                            ]);
                        }
                        $accounts[$user] = $bungieNetAccount;
                    }
                }
            }
        }

        return $accounts;
    }

    /**
     * Resolve linked Destiny memberships for a Bungie.net account.
     */
    public function getLinkedProfiles(BungieNetAccount $bungieNetAccount, bool $lastPlayedPlayer = false): DestinyPlayer|array
    {
        $bungie = new DestinyClient;
        $bungie->getLinkedProfiles(254, $bungieNetAccount->membershipId);
        $linkedProfilesResults = $bungie->get('getLinkedProfiles');
        foreach ($linkedProfilesResults as $linkedProfiles) {
            break;
        }

        if (! isset($linkedProfiles->profiles) || empty($linkedProfiles->profiles)) {
            throw new Exception('No linked players found to your BungieNet account: '.$bungieNetAccount->displayName);
        }

        if ($lastPlayedPlayer) {
            $lastPlayed = false;
            foreach ($linkedProfiles->profiles as $linkedProfile) {
                if ($lastPlayed === false || strtotime($lastPlayed->dateLastPlayed) < strtotime($linkedProfile->dateLastPlayed)) {
                    $lastPlayed = $linkedProfile;
                }
            }

            if (! $destinyPlayer = DestinyBungiePlayer::where([['membership_id', '=', $lastPlayed->membershipId]])->first()) {
                $destinyPlayer = new DestinyBungiePlayer;
                $destinyPlayer->membership_id = $lastPlayed->membershipId;
                $destinyPlayer->membership_type = $lastPlayed->membershipType;
                $destinyPlayer->display_name = $lastPlayed->bungieGlobalDisplayName;
                $destinyPlayer->display_code = $lastPlayed->bungieGlobalDisplayNameCode;
                $destinyPlayer->save();
            } elseif ($destinyPlayer->display_name !== $lastPlayed->bungieGlobalDisplayName || $destinyPlayer->display_code !== $lastPlayed->bungieGlobalDisplayNameCode) {
                $destinyPlayer->display_name = $lastPlayed->bungieGlobalDisplayName;
                $destinyPlayer->display_code = $lastPlayed->bungieGlobalDisplayNameCode;
                $destinyPlayer->save();
            }

            return new DestinyPlayer([
                'id' => $destinyPlayer->id,
                'membershipId' => $destinyPlayer->membership_id,
                'membershipType' => $destinyPlayer->membership_type,
                'displayName' => $destinyPlayer->display_name,
            ]);
        }

        return $linkedProfiles->profiles;
    }

    /**
     * Resolve Destiny memberships for the supplied query data.
     */
    public function getPlayers(CommandContext $command): array
    {
        $players = [];
        if (! empty($command->query->gamertags)) {
            $bungie = new DestinyClient;
            $tempPlayers = [];

            foreach ($command->query->gamertags as $index => $gamertag) {
                if (trim($gamertag) == '') {
                    unset($command->query->gamertags[$index]);

                    continue;
                }

                if (isset($command->query->consoles[$index])) {
                    $tempConsole = $command->query->consoles[$index];
                } elseif (empty($command->query->consoles)) {
                    $tempConsole = $command->defaultConsole;
                } else {
                    $tempConsole = reset($command->query->consoles);
                }

                if ($tempConsole === 254) {
                    $accounts = $this->getAccounts($command, $gamertag);
                    if (isset($accounts[$gamertag])) {
                        $destinyPlayer = $this->getLinkedProfiles($accounts[$gamertag], true);
                        $players[$destinyPlayer->displayName] = $destinyPlayer;
                        unset($command->query->gamertags[$index]);

                        continue;
                    }
                }

                if ($tempConsole === 4 && strpos($gamertag, '#') === false) {
                    $gamertag = $this->formatToHashtag($gamertag);
                    $command->query->gamertags[$index] = $gamertag;
                }

                if (strpos($gamertag, '#') === false) {
                    throw new Exception('Player '.$gamertag.' not found. Please search using your Bungie name');
                }

                [$displayName, $displayNameCode] = explode('#', $gamertag);

                $destinyBungiePlayer = DestinyBungiePlayer::where([
                    ['display_name', '=', $displayName],
                    ['display_code', '=', $displayNameCode],
                ])->first();

                if ($destinyBungiePlayer) {
                    $players[$gamertag] = new DestinyPlayer([
                        'id' => $destinyBungiePlayer->id,
                        'membershipId' => $destinyBungiePlayer->membership_id,
                        'membershipType' => $destinyBungiePlayer->membership_type,
                        'displayName' => $destinyBungiePlayer->display_name,
                    ]);
                } else {
                    $bungie->searchDestinyPlayerByBungieName($displayName, $displayNameCode);
                    $tempPlayers[$gamertag] = $tempConsole;
                }
            }

            if (! empty($tempPlayers)) {
                $playersResults = $bungie->get('searchDestinyPlayerByBungieName');

                foreach ($playersResults as $gamertag => $foundPlayers) {
                    if (empty($foundPlayers)) {
                        if ($tempPlayers[$gamertag] === 4 || strpos($gamertag, '#') !== false) {
                            $gamertag = $this->formatNoBnet($gamertag);
                        }

                        throw new Exception('Player '.$gamertag.' not found. (After the crossplay update you might need to update your commands: https://twitter.com/DestinyCommand/status/1430509496808398854)');
                    }

                    $playersTemp = [];
                    foreach ($foundPlayers as $foundPlayer) {
                        if (isset($foundPlayer->crossSaveOverride) && (
                            $foundPlayer->crossSaveOverride === $foundPlayer->membershipType ||
                            ($foundPlayer->crossSaveOverride === 0 && isset($foundPlayer->applicableMembershipTypes) && ! empty($foundPlayer->applicableMembershipTypes))
                        )) {
                            $destinyBungiePlayer = DestinyBungiePlayer::where('membership_id', $foundPlayer->membershipId)
                                ->orWhere([
                                    ['display_name', '=', $foundPlayer->bungieGlobalDisplayName],
                                    ['display_code', '=', $foundPlayer->bungieGlobalDisplayNameCode],
                                ])->first();

                            if ($destinyBungiePlayer) {
                                if (
                                    $destinyBungiePlayer->membership_type !== $foundPlayer->membershipType ||
                                    $destinyBungiePlayer->membership_id !== $foundPlayer->membershipId ||
                                    $destinyBungiePlayer->display_name !== $foundPlayer->bungieGlobalDisplayName ||
                                    $destinyBungiePlayer->display_code !== $foundPlayer->bungieGlobalDisplayNameCode
                                ) {
                                    $destinyBungiePlayer->membership_type = $foundPlayer->membershipType;
                                    $destinyBungiePlayer->membership_id = $foundPlayer->membershipId;
                                    $destinyBungiePlayer->display_name = $foundPlayer->bungieGlobalDisplayName;
                                    $destinyBungiePlayer->display_code = $foundPlayer->bungieGlobalDisplayNameCode;
                                    $destinyBungiePlayer->save();
                                }
                            } else {
                                $destinyBungiePlayer = new DestinyBungiePlayer;
                                $destinyBungiePlayer->membership_type = $foundPlayer->membershipType;
                                $destinyBungiePlayer->membership_id = $foundPlayer->membershipId;
                                $destinyBungiePlayer->display_name = $foundPlayer->bungieGlobalDisplayName;
                                $destinyBungiePlayer->display_code = $foundPlayer->bungieGlobalDisplayNameCode;
                                $destinyBungiePlayer->save();
                            }

                            $destinyPlayer = new DestinyPlayer([
                                'id' => $destinyBungiePlayer->id,
                                'membershipId' => $destinyBungiePlayer->membership_id,
                                'membershipType' => $destinyBungiePlayer->membership_type,
                                'displayName' => $destinyBungiePlayer->display_name,
                            ]);

                            $playersTemp[] = $destinyPlayer;
                        }
                    }

                    if (! empty($playersTemp)) {
                        $players[$gamertag] = end($playersTemp);
                    }
                }
            }
        } elseif ($command->requestedPlayer !== []) {
            $players[$command->requestedPlayer['displayName']] = new DestinyPlayer([
                'membershipId' => $command->requestedPlayer['membershipId'],
                'membershipType' => $command->requestedPlayer['membershipType'],
                'displayName' => $command->requestedPlayer['displayName'],
            ]);
        }

        return $players;
    }

    /**
     * Normalize a legacy dash-delimited Bungie name to hashtag format.
     */
    private function formatToHashtag(string $gamertag): string
    {
        $position = strrpos($gamertag, '-');
        if ($position !== false) {
            $gamertag = substr_replace($gamertag, '#', $position, 1);
        }

        return $gamertag;
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
