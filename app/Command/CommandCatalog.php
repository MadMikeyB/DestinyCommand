<?php

namespace App\Command;

use App\Enums\CharacterProfileAction;
use App\Enums\CharacterProgressionAction;
use App\Enums\Playlist;
use App\Enums\TrialsReportAction;
use App\Enums\VendorAction;

/**
 * Central catalog for legacy command aliases and metadata.
 */
class CommandCatalog
{
    /**
     * Get supported console aliases mapped to membership types.
     */
    public static function consoleAliases(): array
    {
        return [
            'xbox' => 1,
            'ps' => 2,
            'xb' => 1,
            'xb1' => 1,
            'psn' => 2,
            'playstation' => 2,
            'ps4' => 2,
            'pc' => 3,
            'bnet' => 3,
            'steam' => 3,
            'bungienet' => 254,
        ];
    }

    /**
     * Get action aliases mapped to canonical command keys.
     */
    public static function actionAliases(): array
    {
        return [
            'tw' => 'trialsweekly',
            'tt' => 'trialsteam',
            'kinetic' => 'primary',
            'energy' => 'secondary',
            'power' => 'heavy',
            'loadout' => 'weapons',
            'rmb' => 'ratemybutt',
            'combatrating' => 'cr',
            'winloss' => 'wl',
            'mostkills' => 'mk',
            'nade' => 'grenade',
            'powerlvl' => 'powerlevel',
            'light' => 'powerlevel',
            'pwrlvl' => 'powerlevel',
            'trialscard' => 'card',
        ];
    }

    /**
     * Get vendor command mappings keyed by command name.
     */
    public static function vendorActions(): array
    {
        return VendorAction::mapping();
    }

    /**
     * Get character progression command metadata.
     */
    public static function characterProgressions(): array
    {
        return CharacterProgressionAction::mapping();
    }

    /**
     * Get character profile field mappings.
     */
    public static function characterProfileActions(): array
    {
        return CharacterProfileAction::mapping();
    }

    /**
     * Get Trials Report action mappings.
     */
    public static function trialsReportActions(): array
    {
        return TrialsReportAction::mapping();
    }

    /**
     * Get gear and loadout command mappings.
     */
    public static function gearActions(): array
    {
        return [
            'ghost',
            'vehicle',
            'ship',
            'clan',
            'classitem',
            'emblem',
            'emote',
            'aura',
            'subclass',
            'primary',
            'secondary',
            'heavy',
            'helmet',
            'gauntlet',
            'legs',
            'chest',
            'weapons' => ['primary', 'secondary', 'heavy'],
            'gear' => ['helmet', 'gauntlet', 'chest', 'legs'],
        ];
    }

    /**
     * Get stat command mappings.
     */
    public static function statActions(): array
    {
        return StatActionCatalog::mapping();
    }

    /**
     * Get medal command mappings.
     */
    public static function statMedals(): array
    {
        return StatMedalCatalog::mapping();
    }

    /**
     * Get playlist aliases mapped to activity mode ids.
     */
    public static function playlists(): array
    {
        return Playlist::mapping();
    }

    /**
     * Get Gambit-only stat and medal command keys.
     */
    public static function gambitStats(): array
    {
        return array_merge(StatActionCatalog::gambitKeys(), StatMedalCatalog::gambitKeys());
    }

    /**
     * Get static text responses for plain-text commands.
     */
    public static function textCommands(): array
    {
        return [
            'default_info' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com for help @DestinyCommand on Twitter. Have you tried "!destiny setaccount" yet: https://twitter.com/DestinyCommand/status/1164196373933318144 ',
            'help' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com for help @DestinyCommand on Twitter',
            'commands' => 'Command list: destinycommand.com',
            'setplayer' => 'x',
            'setaccount' => 'x',
            'setxur' => 'x',
            'trialsmap' => "'Trialsmap' command is in development",
            'nightfall' => "'Nightfall' command is in development",
            'elo' => "'ELO' command is in development",
            'donate' => 'If you like the !destiny command and want to support: https://2g.be/u/donate (This money does NOT go to the streamer)',
        ];
    }
}
