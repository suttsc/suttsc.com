<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SiteStatKey;
use App\Integrations\Strava\StravaActivityData;
use App\Integrations\Strava\StravaClient;
use App\Models\StravaActivity;
use App\Services\SiteStats;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Mirrors the last fortnight of Strava into strava_activities.
 */
final class SyncStravaActivities implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function handle(StravaClient $strava, SiteStats $stats): void
    {
        $activities = $strava->activitiesSince(now()->subDays(14));

        StravaActivity::query()->upsert(
            array_map(fn (StravaActivityData $activity): array => [
                'strava_id' => $activity->stravaId,
                'name' => $activity->name,
                'sport_type' => $activity->sportType,
                'distance_metres' => $activity->distanceMetres,
                'moving_seconds' => $activity->movingSeconds,
                'elevation_metres' => $activity->elevationMetres,
                'started_at' => $activity->startedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ], $activities),
            uniqueBy: ['strava_id'],
            update: ['name', 'sport_type', 'distance_metres', 'moving_seconds', 'elevation_metres', 'started_at', 'updated_at'],
        );

        $stats->set(SiteStatKey::StravaSyncedAt, now()->getTimestamp());
    }
}
