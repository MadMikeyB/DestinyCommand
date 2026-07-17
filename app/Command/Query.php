<?php

namespace App\Command;

/**
 * Parses the raw query string into actions, gamertags, and consoles.
 */
class Query
{
    public bool $reqUser = false;

    public array $actions = [];

    public array $gamertags = [];

    public array $consoles = [];

    /**
     * Parse a raw query into actions, gamertags, and console hints.
     */
    public function __construct(string $strQuery)
    {
        $aConsoles = CommandCatalog::consoleAliases();

        $aQuery = $this->splitQueryParts($strQuery);
        $aQuery = $this->mergeSeparatedParts($aQuery);

        // Last parameter is the console
        if ($this->hasTrailingConsole($aQuery, $aConsoles)) {
            $this->consoles[] = $aConsoles[strtolower(array_pop($aQuery))];
        }

        // First parameter is the action
        if (! empty($aQuery)) {
            $aActions = array_unique(explode(';', strtolower(array_shift($aQuery))));
            foreach ($aActions as $strAction) {
                $oAction = new Action($strAction);
                if (! isset($oAction->noUser) || $oAction->noUser === false) {
                    $this->reqUser = true;
                }
                $this->actions[$oAction->key] = $oAction;
            }
        }

        // Whats  left should be gamertags
        if (! empty($aQuery)) {
            $this->gamertags = explode(';', implode(' ', $aQuery));
            if (! empty($this->gamertags)) {
                // You can specify a platform for each gamertag seperately, read them here and overwrite the overall platform
                foreach ($this->gamertags as $i => $strGamertag) {
                    $strGamertag = str_replace('—', '--', $strGamertag); // Double dash fix discord
                    $aGamertag = explode(':', $strGamertag);
                    if (count($aGamertag) === 2) {
                        $this->gamertags[$i] = $aGamertag[0];
                        if (isset($aGamertag[1]) && in_array($aGamertag[1], array_keys($aConsoles))) {
                            $this->consoles[$i] = $aConsoles[$aGamertag[1]];
                        }
                    }
                }
            }
        }
    }

    /**
     * Split the raw query into space-delimited parts.
     */
    private function splitQueryParts(string $strQuery): array
    {
        $strQuery = urldecode($strQuery);
        $strQuery = str_replace([',', '%20'], [';', ' '], $strQuery);

        return array_values(array_diff(explode(' ', $strQuery), ['']));
    }

    /**
     * Merge parts that belong to semicolon-delimited action or player groups.
     */
    private function mergeSeparatedParts(array $aQuery): array
    {
        foreach ($aQuery as $i => $strQueryPart) {
            if (! isset($aQuery[$i + 1])) {
                continue;
            }

            if (substr($strQueryPart, -1) === ';' || $aQuery[$i + 1][0] === ';') {
                $aQuery[$i + 1] = $aQuery[$i + 1].$strQueryPart;
                unset($aQuery[$i]);
            }
        }

        return array_values($aQuery);
    }

    /**
     * Determine whether the final query token is a console alias.
     */
    private function hasTrailingConsole(array $aQuery, array $aConsoles): bool
    {
        if ($aQuery === []) {
            return false;
        }

        return in_array(end($aQuery), array_keys($aConsoles));
    }
}
