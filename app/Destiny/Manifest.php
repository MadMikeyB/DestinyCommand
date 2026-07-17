<?php

namespace App\Destiny;

use App\Transports\BungieTransport;
use Exception;
use Illuminate\Support\Facades\Log;
use SQLite3;
use ZipArchive;

/**
 * Coordinates Destiny manifest downloads, settings, and lookups.
 */
class Manifest
{
    /**
     * Create a new manifest manager instance.
     */
    public function __construct()
    {
        $this->manifest_path = storage_path().'/manifest/';
        $this->setting_file = $this->manifest_path.'settings.json';
        $this->ensureManifestDirectoryExists();
        $this->settings = $this->loadSettings();
    }

    /**
     * Ensure the manifest storage directory exists.
     */
    private function ensureManifestDirectoryExists(): void
    {
        if (! file_exists($this->manifest_path)) {
            mkdir($this->manifest_path, 0777, true);
        }
    }

    /**
     * Ensure the local manifest cache and metadata are available.
     */
    private function ensureManifestReady(): void
    {
        $database = $this->getSetting('database');
        $tables = $this->getSetting('tables');

        if ($database && is_object($tables)) {
            $cacheFilePath = $this->cacheFilePath($database);

            if (file_exists($cacheFilePath)) {
                return;
            }
        }

        Log::info('Manifest cache missing or incomplete, attempting refresh.');
        $result = $this->check();

        Log::info('Manifest refresh result', [
            'result' => $result,
        ]);

        $database = $this->getSetting('database');
        $tables = $this->getSetting('tables');
        $cacheFilePath = $database ? $this->cacheFilePath($database) : null;

        if (! $database || ! is_object($tables) || ! $cacheFilePath || ! file_exists($cacheFilePath)) {
            Log::error('DC530 manifest cache is not available after refresh attempt', [
                'code' => 'DC530',
                'database' => $database,
                'cache_file' => $cacheFilePath,
                'tables_type' => gettype($tables),
            ]);

            throw new Exception('Manifest is not initialized (#DC530)');
        }
    }

    /**
     * Download and refresh the local manifest when Bungie publishes a new one.
     */
    public function check(): string
    {
        $oBungie = new DestinyClient;
        $oBungie->getDestinyManifest();

        $oCheck = $oBungie->get('getDestinyManifest')['getDestinyManifest'];

        if (isset($oCheck->mobileWorldContentPaths->en)) {
            $strDatabase = $oCheck->mobileWorldContentPaths->en;
            if ($this->getSetting('database') != $strDatabase) {
                // New database found.
                $aTables = $this->updateManifest($strDatabase);
                $this->setSetting('database', $strDatabase);
                $this->setSetting('tables', $aTables);

                return 'Manifest updated';
            } else {
                return 'Manifest already up-to-date';
            }
        } else {
            return 'Bungie error, failed to update manifest';
        }
    }

    /**
     * Download, extract, and inspect a new manifest database.
     */
    private function updateManifest(string $strDatabase): array
    {
        $response = (new BungieTransport)
            ->pendingRequest(includeOrigin: true)
            ->get('https://bungie.net'.$strDatabase);

        $zData = $response->body();

        $strCachePath = $this->cacheFilePath($strDatabase);
        if (! file_exists(dirname($strCachePath))) {
            mkdir(dirname($strCachePath), 0777, true);
        }
        file_put_contents($strCachePath.'.zip', $zData);

        $zZip = new ZipArchive;
        if ($zZip->open($strCachePath.'.zip') === true) {
            $zZip->extractTo($this->manifest_path.'cache');
            $zZip->close();
        }

        $aTables = [];
        if ($db = new SQLite3($strCachePath)) {
            $oResult = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
            while ($aRow = $oResult->fetchArray()) {
                $aTable = [];
                $oResult2 = $db->query('PRAGMA table_info('.$aRow['name'].')');
                while ($aRow2 = $oResult2->fetchArray()) {
                    $aTable[] = $aRow2[1];
                }
                $aTables[$aRow['name']] = $aTable;
            }
        }

        return $aTables;
    }

    /**
     * Load persisted manifest settings.
     */
    public function loadSettings(): object
    {
        if (! file_exists($this->setting_file)) {
            return (object) [];
        }

        return json_decode(file_get_contents($this->setting_file));
    }

    /**
     * Persist a manifest setting value.
     */
    public function setSetting(string $name, mixed $value): void
    {
        $this->ensureManifestDirectoryExists();
        $this->settings->{$name} = $value;
        file_put_contents($this->setting_file, json_encode($this->settings));
    }

    /**
     * Read a manifest setting value.
     */
    public function getSetting(string $name): mixed
    {
        if (isset($this->settings->{$name})) {
            return $this->settings->{$name};
        }

        return '';
    }

    /**
     * Run a read-only query against the local manifest database.
     */
    public function queryManifest(string $strQuery): array
    {
        return $this->repository()->query($strQuery);
    }

    /**
     * Browse all rows in a manifest definition table.
     */
    public function browseDefinition(string $strTableName): array
    {
        return $this->repository()->browseDefinition($strTableName);
    }

    /**
     * Resolve a single manifest definition by table suffix and id.
     */
    public function getDefinition(string $strTableName, int|string $id): object|false
    {
        $this->ensureManifestReady();

        $aTables = $this->getSetting('tables');
        $definitionTableName = 'Destiny'.$strTableName.'Definition';

        if (! is_object($aTables) || ! isset($aTables->{$definitionTableName}[0])) {
            Log::error('DC531 manifest table metadata missing', [
                'code' => 'DC531',
                'table' => $definitionTableName,
                'tables_type' => gettype($aTables),
            ]);

            throw new Exception('Manifest table metadata is missing (#DC531)');
        }

        return $this->repository()->getDefinition($strTableName, $id);
    }

    /**
     * Build a repository for the currently configured manifest database.
     */
    private function repository(): ManifestRepository
    {
        $this->ensureManifestReady();

        return new ManifestRepository(
            $this->cacheFilePath($this->getSetting('database')),
            $this->getSetting('tables'),
        );
    }

    /**
     * Resolve the local cache file path for a manifest database URL.
     */
    private function cacheFilePath(string $database): string
    {
        return $this->manifest_path.'cache/'.pathinfo($database, PATHINFO_BASENAME);
    }
}
