<?php

use App\Enums\SiteStatKey;
use App\Jobs\SyncStravaActivities;
use App\Services\SiteStats;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('site:deployed', function (SiteStats $stats) {
    $stats->increment(SiteStatKey::DeployCount);

    $this->info("Deploy #{$stats->deployCount()} recorded.");
})->purpose('Count a deploy, for /uptime. Run it from the deploy script.');

Schedule::job(new SyncStravaActivities)
    ->hourly()
    ->when(fn (): bool => filled(config('services.strava.refresh_token')));
