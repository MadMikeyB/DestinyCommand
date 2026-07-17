<?php

use App\Console\Commands\UpdateDestinyManifest;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UpdateDestinyManifest::class)->daily();
