<?php

namespace App\Models\Destiny;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores resolved Bungie-name based Destiny memberships for reuse.
 */
#[Fillable([
    'membership_id',
    'membership_type',
    'display_name',
    'display_code',
])]
class DestinyBungiePlayer extends Model {}
