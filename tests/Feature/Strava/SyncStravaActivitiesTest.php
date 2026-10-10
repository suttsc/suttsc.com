<?php

declare(strict_types=1);

use App\Enums\SiteStatKey;
use App\Integrations\Strava\FakeStravaClient;
use App\Integrations\Strava\StravaActivityData;
use App\Integrations\Strava\StravaClient;
use App\Jobs\SyncStravaActivities;
use App\Models\SiteStat;
use App\Models\StravaActivity;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;

function activity(int $id, string $name, string $startedAt, int $metres = 5000): StravaActivityData
{
    return new StravaActivityData($id, $name, 'Run', $metres, 1500, 20, CarbonImmutable::parse($startedAt));
}

it('mirrors recent activities into the local table and notes when', function () {
    $this->travelTo('2026-10-10 12:00:00');
    app()->instance(StravaClient::class, new FakeStravaClient([
        activity(1, 'Morning', '2026-10-09 07:00:00'),
        activity(2, 'Ancient', '2026-09-01 07:00:00'),
    ]));

    SyncStravaActivities::dispatchSync();

    expect(StravaActivity::query()->pluck('name')->all())->toBe(['Morning'])
        ->and(SiteStat::query()->find(SiteStatKey::StravaSyncedAt->value)?->value)->toBe(now()->getTimestamp());
});

it('updates an activity it has already mirrored instead of duplicating it', function () {
    $this->travelTo('2026-10-10 12:00:00');
    StravaActivity::factory()->create(['strava_id' => 1, 'name' => 'Before', 'started_at' => '2026-10-09 07:00:00']);
    app()->instance(StravaClient::class, new FakeStravaClient([
        activity(1, 'Renamed', '2026-10-09 07:00:00', metres: 6000),
    ]));

    SyncStravaActivities::dispatchSync();

    expect(StravaActivity::query()->sole())
        ->name->toBe('Renamed')
        ->distance_metres->toBe(6000);
});

it('is scheduled hourly only once strava is configured', function (?string $token, bool $scheduled) {
    config(['services.strava.refresh_token' => $token]);

    $this->artisan('schedule:list')
        ->assertSuccessful();

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->description ?? '', SyncStravaActivities::class));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 * * * *')
        ->and($event->filtersPass(app()))->toBe($scheduled);
})->with([
    'configured' => ['refresh-token', true],
    'not configured' => [null, false],
]);
