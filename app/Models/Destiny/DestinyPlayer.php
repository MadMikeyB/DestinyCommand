<?php

namespace App\Models\Destiny;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a resolved Destiny membership used by command execution.
 */
#[Fillable(['id', 'membershipId', 'membershipType', 'displayName', 'account_id', 'is_default'])]
class DestinyPlayer extends Model {}
