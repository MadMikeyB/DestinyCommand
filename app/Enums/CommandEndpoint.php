<?php

namespace App\Enums;

/**
 * Enumerates the upstream endpoint families used by command actions.
 */
enum CommandEndpoint: string
{
    case VENDOR = 'vendor';
    case PROFILE = 'profile';
    case STATS = 'stats';
    case GET_FIRETEAM = 'getFireteam';
}
