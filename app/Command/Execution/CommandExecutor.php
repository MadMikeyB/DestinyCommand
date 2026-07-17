<?php

namespace App\Command\Execution;

use App\Command\CommandContext;
use App\Command\Resolution\CommandPlayerResolver;
use App\Models\Destiny\DestinyPlayer;
use App\Services\SetPlayer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;

/**
 * Executes parsed commands against the configured providers.
 */
class CommandExecutor
{
    /**
     * Create a new command executor instance.
     */
    public function __construct(
        private CommandPlayerResolver $commandPlayerResolver,
    ) {}

    /**
     * Execute a parsed command and collect provider responses.
     *
     * @return array{players: array, response: array, prep: mixed}
     */
    public function execute(CommandContext $command): array
    {
        $players = [];
        $response = [];
        $prep = [];

        if ($command->query->reqUser === true) {
            $players = $this->commandPlayerResolver->getPlayers($command);
            if (empty($players)) {
                $players = $this->getFallbackPlayers($command);
            }

            if (empty($players)) {
                throw new Exception('No players found');
            }
        }

        $providers = [];

        foreach ($command->query->actions as $action) {
            if ($action->provider === 'plain_text') {
                continue;
            }

            $providerClass = $action->provider;
            $provider = $providers[$action->provider] ??= app($providerClass);
            $prep = $provider->fetch($action, ['players' => $players], true);
        }

        foreach ($command->query->actions as $action) {
            if ($action->key === 'setplayer') {
                $players = $this->commandPlayerResolver->getPlayers($command);
                if (empty($players)) {
                    $action->text = 'No player info provided';
                } else {
                    foreach ($players as $player) {
                        break;
                    }
                    $setplayer = new SetPlayer;

                    if ($setplayer->setPlayer($player)) {
                        $action->text = 'Succesfully saved player: '.$player->displayName;
                    } else {
                        $action->text = 'Something went wrong saving player: '.$player->displayName.'. Note: setplayer is a Nightbot only feature';
                    }
                }
            } elseif ($action->key === 'setaccount') {
                $accounts = $this->commandPlayerResolver->getAccounts($command);
                if (empty($accounts)) {
                    $action->text = 'No account info provided';
                } else {
                    foreach ($accounts as $account) {
                        break;
                    }
                    $setplayer = new SetPlayer;

                    if ($setplayer->setAccount($account)) {
                        $action->text = 'Succesfully saved BungieNet account: '.$account->displayName;
                    } else {
                        $action->text = 'Something went wrong saving BungieNet account: '.$account->displayName.'. Note: setaccount is a Nightbot only feature';
                    }
                }
            } elseif ($action->key === 'setxur') {
                if ($this->isModerator($command)) {
                    $location = $this->getTextFromQuery($command);
                    if (! $location) {
                        $action->text = 'No location info provided';
                    } else {
                        Cache::put('xur-location', $location, Carbon::parse('next friday 17:00:00'));
                        $action->text = 'Successfully saved Xur location';
                    }
                } else {
                    $action->text = 'Not allowed to set Xur location';
                }
            }

            $provider = $providers[$action->provider] ?? null;
            $result = $action->provider === 'plain_text'
                ? ['text' => [$action->text]]
                : $provider->fetch($action, ['players' => $players], false);

            if (is_array($result)) {
                $response = $this->mergeArrays($response, $result);
            } else {
                $response[$action->key] = $result;
            }
        }

        return [
            'players' => $players,
            'response' => $response,
            'prep' => $prep,
        ];
    }

    /**
     * Resolve saved Nightbot-linked fallback players when the query omits one.
     */
    private function getFallbackPlayers(CommandContext $command): array
    {
        $players = [];
        $setplayer = new SetPlayer;
        $userPlayer = $setplayer->getPlayer();

        if (! $userPlayer) {
            return $players;
        }

        if ($userPlayer->bungieNetAccountId !== 0) {
            $destinyPlayer = $this->commandPlayerResolver->getLinkedProfiles($userPlayer->bungieNetAccount, true);
            $players[$destinyPlayer->displayName] = $destinyPlayer;
        }

        if (empty($players) && $userPlayer->destinyPlayerId !== 0) {
            $players[$userPlayer->destinyPlayer->display_name] = new DestinyPlayer([
                'id' => $userPlayer->destinyPlayer->id,
                'membershipId' => $userPlayer->destinyPlayer->membership_id,
                'membershipType' => $userPlayer->destinyPlayer->membership_type,
                'displayName' => $userPlayer->destinyPlayer->display_name,
            ]);
        }

        return $players;
    }

    /**
     * Determine whether the current user may run moderator-only commands.
     */
    private function isModerator(CommandContext $command): bool
    {
        $moderatorKeys = config('destinycommand.moderator_keys', []);
        $userKey = sha1($command->user.$command->channel.$command->token);

        return in_array($userKey, $moderatorKeys, true);
    }

    /**
     * Extract freeform text payloads from the parsed query.
     */
    private function getTextFromQuery(CommandContext $command): string|false
    {
        $text = '';
        if (isset($command->query->gamertags[0])) {
            $text = trim($command->query->gamertags[0]);
        }

        return $text === '' ? false : $text;
    }

    /**
     * Merge provider response arrays recursively while preserving keys.
     */
    private function mergeArrays(array $first, array $second): array
    {
        foreach ($second as $key => $value) {
            if (array_key_exists($key, $first) && is_array($value)) {
                $first[$key] = $this->mergeArrays($first[$key], $second[$key]);
            } else {
                $first[$key] = $value;
            }
        }

        return $first;
    }
}
