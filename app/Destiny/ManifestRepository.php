<?php

namespace App\Destiny;

use Exception;
use SQLite3;

/**
 * Provides read-only access to the local Destiny manifest SQLite database.
 */
class ManifestRepository
{
    /**
     * Build a repository for a manifest database path and table metadata map.
     */
    public function __construct(
        private string $databasePath,
        private object $tables,
    ) {}

    /**
     * Run a read-only query against the manifest database.
     */
    public function query(string $query): array
    {
        $results = [];

        if ($database = $this->openDatabase()) {
            $queryResult = $database->query($query);
            while ($row = $queryResult->fetchArray()) {
                $key = is_numeric($row[0]) ? sprintf('%u', $row[0] & 0xFFFFFFFF) : $row[0];
                $results[$key] = json_decode($row[1]);
            }
        }

        return $results;
    }

    /**
     * Browse all rows from a definition table.
     */
    public function browseDefinition(string $tableName): array
    {
        return $this->query('SELECT * FROM '.$tableName);
    }

    /**
     * Resolve a single definition row using the manifest metadata map.
     */
    public function getDefinition(string $tableName, int|string $id): object|false
    {
        $definitionTableName = 'Destiny'.$tableName.'Definition';
        $definitionKey = $this->definitionKey($definitionTableName);
        $results = $this->query('SELECT * FROM '.$definitionTableName.$this->whereClause($definitionKey, $id));

        return $results[(string) $id] ?? false;
    }

    /**
     * Open the SQLite manifest database.
     */
    private function openDatabase(): SQLite3|false
    {
        return new SQLite3($this->databasePath);
    }

    /**
     * Resolve the primary key column for a manifest definition table.
     */
    private function definitionKey(string $tableName): string
    {
        if (! isset($this->tables->{$tableName}[0])) {
            throw new Exception('Manifest table metadata is missing (#DC531)');
        }

        return $this->tables->{$tableName}[0];
    }

    /**
     * Build the lookup predicate for a manifest definition id.
     */
    private function whereClause(string $definitionKey, int|string $id): string
    {
        if (is_numeric($id)) {
            return ' WHERE '.$definitionKey.'='.$id.' OR '.$definitionKey.'='.($id - 4294967296);
        }

        return ' WHERE '.$definitionKey.'="'.$id.'"';
    }
}
