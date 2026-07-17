<?php

namespace App\Enums;

use App\Command\Providers\BungieProvider;
use App\Command\Providers\TrialsReportProvider;

/**
 * Enumerates the upstream providers used by command actions.
 */
enum CommandProvider: string
{
    case BUNGIE = BungieProvider::class;
    case TRIALS_REPORT = TrialsReportProvider::class;
    case PLAIN_TEXT = 'plain_text';
}
