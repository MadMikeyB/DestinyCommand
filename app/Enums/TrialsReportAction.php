<?php

namespace App\Enums;

/**
 * Enumerates Trials Report-backed command actions.
 */
enum TrialsReportAction: string
{
    case TRIALS_TEAM = 'trialsteam';
    case TRIALS_WEEKLY = 'trialsweekly';

    /**
     * Get the upstream endpoint for the command action.
     */
    public function endpoint(): string
    {
        return 'getFireteam';
    }

    /**
     * Get the command-key to endpoint mapping.
     */
    public static function mapping(): array
    {
        $mapping = [];

        foreach (self::cases() as $case) {
            $mapping[$case->value] = $case->endpoint();
        }

        return $mapping;
    }
}
