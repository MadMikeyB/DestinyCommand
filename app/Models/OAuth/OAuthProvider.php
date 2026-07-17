<?php

namespace App\Models\OAuth;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores external OAuth provider configuration for legacy auth flows.
 */
#[Table('oauth_providers')]
#[Fillable(['name', 'auth_url', 'token_url', 'client_id', 'client_secret', 'scope', 'redirect_url', 'local_redirect'])]
class OAuthProvider extends Model {}
