<?php

namespace App\Enums;

/**
 * Enumerates inventory bucket command keys and their bucket hashes.
 */
enum InventoryBucket: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case HEAVY = 'heavy';
    case HELMET = 'helmet';
    case GAUNTLET = 'gauntlet';
    case CHEST = 'chest';
    case LEGS = 'legs';
    case CLASS_ITEM = 'classitem';
    case GHOST = 'ghost';
    case VEHICLE = 'vehicle';
    case SHIP = 'ship';
    case SUBCLASS = 'subclass';
    case CLAN = 'clan';
    case EMBLEM = 'emblem';
    case EMOTE = 'emote';
    case AURA = 'aura';

    /**
     * Get the bucket hash for the command key.
     */
    public function hash(): int
    {
        return match ($this) {
            self::PRIMARY => 1498876634,
            self::SECONDARY => 2465295065,
            self::HEAVY => 953998645,
            self::HELMET => 3448274439,
            self::GAUNTLET => 3551918588,
            self::CHEST => 14239492,
            self::LEGS => 20886954,
            self::CLASS_ITEM => 1585787867,
            self::GHOST => 4023194814,
            self::VEHICLE => 2025709351,
            self::SHIP => 284967655,
            self::SUBCLASS => 3284755031,
            self::CLAN => 4292445962,
            self::EMBLEM => 4274335291,
            self::EMOTE => 3054419239,
            self::AURA => 1269569095,
        };
    }
}
