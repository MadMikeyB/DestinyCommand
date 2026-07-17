<?php

namespace App\Services;

use App\Models\UserPlayer;

/**
 * Stores and retrieves Nightbot-linked default Destiny identities.
 */
class SetPlayer
{
    private $user = null;

    /**
     * Capture the Nightbot user context from the current request.
     */
    public function __construct()
    {
        $nightbotUser = request()->header('Nightbot-User');

        if ($nightbotUser) {
            parse_str($nightbotUser, $user);

            if (! empty($user['provider']) && ! empty($user['providerId'])) {
                $this->user = (object) $user;
            }
        }
    }

    /**
     * Get the persisted player mapping for the current Nightbot user.
     */
    public function getPlayer()
    {
        if ($this->user) {
            $oUserPlayer = UserPlayer::where([['provider', '=', $this->user->provider], ['providerId', '=', $this->user->providerId]])->first();
            if ($oUserPlayer) {
                return $oUserPlayer;
            }
        }

        return false;
    }

    /**
     * Persist a Bungie.net account mapping for the current Nightbot user.
     */
    public function setAccount($oBungieNetAccount)
    {
        if ($this->user) {
            $oUserPlayer = $this->getPlayer();
            if ($oUserPlayer) {
                if ($oUserPlayer->bungieNetAccount?->membershipId !== $oBungieNetAccount->membershipId) {
                    $oUserPlayer->bungieNetAccountId = $oBungieNetAccount->id;
                    $oUserPlayer->save();
                }
            } else {
                $oUserPlayer = new UserPlayer;
                $oUserPlayer->provider = $this->user->provider;
                $oUserPlayer->providerId = $this->user->providerId;
                $oUserPlayer->bungieNetAccountId = $oBungieNetAccount->id;
                $oUserPlayer->save();
            }

            return $oUserPlayer;
        }

        return false;
    }

    /**
     * Persist a Destiny player mapping for the current Nightbot user.
     */
    public function setPlayer($oDestinyPlayer)
    {
        if ($this->user) {
            $oUserPlayer = $this->getPlayer();
            if ($oUserPlayer) {
                // Update player?
                if (! $oUserPlayer->destinyPlayer || $oUserPlayer->destinyPlayer->membership_id !== $oDestinyPlayer->membershipId) {
                    $oUserPlayer->destinyPlayerId = $oDestinyPlayer->id;
                    $oUserPlayer->save();
                }
            } else {
                // New user player
                $oUserPlayer = new UserPlayer;
                $oUserPlayer->provider = $this->user->provider;
                $oUserPlayer->providerId = $this->user->providerId;
                $oUserPlayer->destinyPlayerId = $oDestinyPlayer->id;
                $oUserPlayer->save();
            }

            return $oUserPlayer;
        }

        return false;
    }
}
