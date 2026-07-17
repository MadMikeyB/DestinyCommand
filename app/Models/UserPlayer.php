<?php

namespace App\Models;

use App\Models\Destiny\BungieNetAccount;
use App\Models\Destiny\DestinyBungiePlayer;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Maps a chat platform user to saved Bungie and Destiny identities.
 */
#[Fillable(['provider', 'providerId', 'destinyPlayerId', 'bungieNetAccountId'])]
class UserPlayer extends Model
{
    /**
     * Get the saved Destiny profile for this chat user.
     */
    public function destinyPlayer(): BelongsTo
    {
        return $this->belongsTo(DestinyBungiePlayer::class, 'destinyPlayerId');
    }

    /**
     * Get the saved Bungie.net account for this chat user.
     */
    public function bungieNetAccount(): BelongsTo
    {
        return $this->belongsTo(BungieNetAccount::class, 'bungieNetAccountId');
    }
}
