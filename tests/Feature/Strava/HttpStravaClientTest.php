<?php

declare(strict_types=1);

use App\Integrations\Strava\HttpStravaClient;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function stravaClient(): HttpStravaClient
{
    return new HttpStravaClient(app(Factory::class), app('cache.store'), 'client', 'secret', 'from-config');
}

it('refreshes the token, keeps the rotated one and fetches activities', function () {
    Http::fake([
        'www.strava.com/oauth/token' => Http::response(['access_token' => 'access', 'refresh_token' => 'rotated']),
        'www.strava.com/api/v3/athlete/activities*' => Http::response([[
            'id' => 42,
            'name' => 'Lunch Run',
            'sport_type' => 'Run',
            'distance' => 5012.4,
            'moving_time' => 1500,
            'total_elevation_gain' => 12.6,
            'start_date' => '2026-10-09T12:00:00Z',
        ]]),
    ]);

    $activities = stravaClient()->activitiesSince(now()->subDays(14));

    expect($activities)->toHaveCount(1)
        ->and($activities[0])
        ->stravaId->toBe(42)
        ->distanceMetres->toBe(5012)
        ->elevationMetres->toBe(13)
        ->and(Cache::get('strava.refresh_token'))->toBe('rotated');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://www.strava.com/oauth/token'
        && $request['refresh_token'] === 'from-config');
    Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer access'));
});

it('uses the rotated refresh token on the next run', function () {
    Cache::forever('strava.refresh_token', 'rotated');
    Http::fake([
        'www.strava.com/oauth/token' => Http::response(['access_token' => 'access', 'refresh_token' => 'rotated-again']),
        'www.strava.com/api/v3/*' => Http::response([]),
    ]);

    stravaClient()->activitiesSince(now());

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://www.strava.com/oauth/token'
        && $request['refresh_token'] === 'rotated');
});
