<?php

namespace App\Models\Destiny;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores Bungie.net accounts discovered during command resolution.
 */
#[Fillable(['membershipId', 'uniqueName', 'displayName'])]
class BungieNetAccount extends Model {}
