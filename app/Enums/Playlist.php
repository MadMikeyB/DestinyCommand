<?php

namespace App\Enums;

/**
 * Enumerates canonical playlist command keys and their activity mode ids.
 */
enum Playlist: string
{
    case STORY = 'story';
    case STRIKE = 'strike';
    case RAID = 'raid';
    case PVP = 'pvp';
    case PATROL = 'patrol';
    case PVE = 'pve';
    case CONTROL = 'control';
    case CLASH = 'clash';
    case NIGHTFALL = 'nightfall';
    case IRON_BANNER = 'ironbanner';
    case SUPREMACY = 'supremacy';
    case SURVIVAL = 'survival';
    case COUNTDOWN = 'countdown';
    case TRIALS = 'trials';
    case SOCIAL = 'social';
    case RUMBLE = 'rumble';
    case DOUBLES = 'doubles';
    case GAMBIT = 'gambit';
    case GAMBIT_PRIME = 'gambitprime';

    /**
     * Get the activity mode id for the playlist.
     */
    public function mode(): int
    {
        return match ($this) {
            self::STORY => 2,
            self::STRIKE => 3,
            self::RAID => 4,
            self::PVP => 5,
            self::PATROL => 6,
            self::PVE => 7,
            self::CONTROL => 10,
            self::CLASH => 12,
            self::NIGHTFALL => 16,
            self::IRON_BANNER => 19,
            self::SUPREMACY => 31,
            self::SURVIVAL => 37,
            self::COUNTDOWN => 38,
            self::TRIALS => 84,
            self::SOCIAL => 40,
            self::RUMBLE => 48,
            self::DOUBLES => 50,
            self::GAMBIT => 63,
            self::GAMBIT_PRIME => 75,
        };
    }

    /**
     * Get the playlist command-key to mode-id mapping, including legacy aliases.
     */
    public static function mapping(): array
    {
        $mapping = [];

        foreach (self::cases() as $case) {
            $mapping[$case->value] = $case->mode();
        }

        $mapping['ib'] = self::IRON_BANNER->mode();
        $mapping['gbp'] = self::GAMBIT_PRIME->mode();

        return $mapping;
    }
}
