<?php

namespace App\Console\Commands;

use App\Destiny\Manifest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateDestinyManifest extends Command
{
    protected $signature = 'bungie:update-manifest';

    protected $description = 'Fetch and store the latest Destiny 2 manifest SQLite cache.';

    public function handle(): int
    {
        $this->info('Checking Bungie manifest metadata...');

        try {
            $result = (new Manifest)->check();

            Log::info('Bungie manifest command completed', [
                'result' => $result,
            ]);

            if ($result === 'Bungie error, failed to update manifest') {
                $this->error($result);

                return self::FAILURE;
            }

            $this->info($result);

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            Log::error('DC532 manifest update command failed', [
                'code' => 'DC532',
                'message' => $throwable->getMessage(),
                'exception' => $throwable::class,
            ]);

            $this->error('Manifest update failed (#DC532)');

            return self::FAILURE;
        }
    }
}
