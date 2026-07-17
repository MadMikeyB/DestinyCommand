<?php

namespace App\Models\OAuth;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores persisted OAuth access and refresh tokens.
 */
#[Table('oauth_sessions')]
#[Fillable(['access_token', 'refresh_token', 'expires_in', 'refresh_expires_in', 'identifier', 'provider_id'])]
class OAuthSession extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_in' => 'datetime',
            'refresh_expires_in' => 'datetime',
        ];
    }
}
