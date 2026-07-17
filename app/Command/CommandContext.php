<?php

namespace App\Command;

/**
 * Holds the normalized state for a single command request.
 */
class CommandContext
{
    public $user;

    public $userId = 0;

    public $channelId = 0;

    public $channel;

    public $query;

    public $token;

    public $platform;

    public $bot;

    public $defaultConsole;

    public $responseUser;

    public array $requestedPlayer = [];

    /**
     * Create a new command context instance.
     */
    public function __construct() {}

    /**
     * Set the requesting user and default response target.
     */
    public function setUser(string $strUser): void
    {
        $this->user = $strUser;
        $this->responseUser = $strUser;
    }

    /**
     * Override the response target for formatted output.
     */
    public function setResponseUser(?string $strUser): void
    {
        if (! is_null($strUser) && trim($strUser) !== '') {
            $strUser = trim($strUser);
            if ($strUser[0] === '@' && strlen($strUser) > 1) {
                $strUser = substr($strUser, 1);
            }
            $this->responseUser = $strUser;
        }
    }

    /**
     * Set the provider user identifier.
     */
    public function setUserId(int|string $iUserId): void
    {
        $this->userId = $iUserId;
    }

    /**
     * Set a fallback player payload supplied directly by the request.
     */
    public function setRequestedPlayer(string $membershipId, string $membershipType, string $displayName): void
    {
        $this->requestedPlayer = [
            'membershipId' => $membershipId,
            'membershipType' => $membershipType,
            'displayName' => $displayName,
        ];
    }

    /**
     * Set the incoming bot token used by moderator commands.
     */
    public function setToken(string $strToken): void
    {
        $this->token = $strToken;
    }

    /**
     * Set the originating channel name.
     */
    public function setChannel(string $strChannel): void
    {
        $this->channel = $strChannel;
    }

    /**
     * Set the originating channel identifier.
     */
    public function setChannelId(int|string $iChannelId): void
    {
        $this->channelId = $iChannelId;
    }

    /**
     * Parse the raw command query into action and player segments.
     */
    public function setQuery(?string $strQuery): void
    {
        if (is_null($strQuery)) {
            $strQuery = 'default_info';
        }
        $this->query = new Query($strQuery);
    }

    /**
     * Set the bot integration issuing the request.
     */
    public function setBot(?string $strBot): void
    {
        if (is_null($strBot)) {
            $strBot = 'nightbot';
        }

        $this->bot = strtolower($strBot);
    }

    /**
     * Set the fallback console used when the query omits one.
     */
    public function setDefaultConsole(string $strConsole): void
    {
        $aConsoles = ['xbox' => 1, 'ps' => 2, 'xb' => 1, 'xb1' => 1, 'psn' => 2, 'playstation' => 2, 'ps4' => 2, 'pc' => 3, 'bnet' => 3, 'steam' => 3];
        $this->defaultConsole = $aConsoles[$strConsole] ?? 1;
    }

    /**
     * Normalize the transport/platform for the current request.
     */
    public function setPlatform(string $strProvider): void
    {
        $aProviders = ['youtube', 'twitch', 'discord', 'slack', 'json'];
        if (! in_array(strtolower(trim($strProvider)), $aProviders, true)) {
            $strProvider = 'twitch';
        }
        $this->platform = strtolower(trim($strProvider));
    }
}
