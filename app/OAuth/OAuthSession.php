<?php

namespace App\OAuth;

use Illuminate\Database\Eloquent\Model;

class OAuthSession extends Model
{
    protected $table = 'oauth_sessions';

    protected $fillable = ['access_token', 'refresh_token', 'expires_in', 'refresh_expires_in', 'identifier', 'provider_id'];

    protected $casts = [
        'expires_in' => 'datetime',
        'refresh_expires_in' => 'datetime',
    ];
}
