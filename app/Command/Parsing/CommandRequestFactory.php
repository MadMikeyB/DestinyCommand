<?php

namespace App\Command\Parsing;

use App\Command\CommandContext;
use Illuminate\Http\Request;

/**
 * Builds a normalized command context from an incoming HTTP request.
 */
class CommandRequestFactory
{
    /**
     * Create command data from an HTTP request.
     *
     * @return array{command: CommandContext, display_username: bool, display_gamertag: bool}
     */
    public function createFromRequest(Request $request): array
    {
        $command = new CommandContext;

        if ($request->header('Nightbot-Channel')) {
            parse_str($request->header('Nightbot-Channel'), $nightbotChannel);
            $command->setChannel($nightbotChannel['displayName']);
            $command->setPlatform($nightbotChannel['provider']);
            $command->setBot('nightbot');
        } else {
            $command->setPlatform($request->input('platform', 'twitch'));
            $command->setBot($request->input('bot', 'nightbot'));

            if ($request->has('channel')) {
                $command->setChannel($request->input('channel'));
            }
        }

        if ($request->header('Nightbot-User')) {
            parse_str($request->header('Nightbot-User'), $nightbotUser);
            $command->setUser($nightbotUser['displayName']);
            $command->setUserId($nightbotUser['providerId']);
            $command->setPlatform($nightbotUser['provider']);
        } else {
            $command->setUser($request->input('user', 'System'));
        }

        if ($request->has('token')) {
            $command->setToken($request->input('token'));
        }

        $command->setDefaultConsole($request->input('default_console', 'xbox'));
        $command->setResponseUser($request->input('response_user', ''));
        $command->setQuery($request->input('query'));

        if ($request->has(['membershipId', 'membershipType', 'displayName'])) {
            $command->setRequestedPlayer(
                (string) $request->input('membershipId'),
                (string) $request->input('membershipType'),
                (string) $request->input('displayName'),
            );
        }

        return [
            'command' => $command,
            'display_username' => ! $request->has('nousername'),
            'display_gamertag' => ! $request->has('nogamertag'),
        ];
    }
}
