<?php

declare(strict_types=1);

use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use App\Models\StravaActivity;
use App\Terminal\Commands\StravaCommand;
use App\Terminal\Output\Window;

it('returns window output', function () {
    expect(app(StravaCommand::class)->handle())->toBeInstanceOf(Window::class);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/strava')
        ));
});

it('shows the last seven days of runs from the local mirror', function () {
    $this->travelTo('2026-10-10 12:00:00');
    StravaActivity::factory()->create([
        'name' => 'Easy one',
        'distance_metres' => 8200,
        'moving_seconds' => 2558,
        'elevation_metres' => 64,
        'started_at' => '2026-10-05 07:00:00',
    ]);
    StravaActivity::factory()->create(['distance_metres' => 5000, 'started_at' => '2026-10-08 07:00:00']);
    StravaActivity::factory()->create(['started_at' => '2026-10-01 07:00:00']);
    StravaActivity::factory()->create(['sport_type' => 'Ride', 'started_at' => '2026-10-09 07:00:00']);
    SiteStat::factory()->create(['key' => SiteStatKey::StravaSyncedAt, 'value' => now()->subMinutes(40)->getTimestamp()]);

    $this->postJson(route('terminal.run'), ['command' => '/strava'])
        ->assertOk()
        ->assertJsonPath('component', 'strava')
        ->assertJsonCount(2, 'props.runs')
        ->assertJsonPath('props.runs.0', [
            'day' => 'Mon',
            'name' => 'Easy one',
            'distance' => '8.2 km',
            'pace' => '5:12/km',
            'elevation' => '64m',
        ])
        ->assertJsonPath('props.total', '13.2 km')
        ->assertJsonPath('props.syncedAgo', '40 minutes ago');
});

it('copes with never having synced', function () {
    $this->postJson(route('terminal.run'), ['command' => '/strava'])
        ->assertJsonPath('props.runs', [])
        ->assertJsonPath('props.total', '0.0 km')
        ->assertJsonPath('props.syncedAgo', null);
});

it('renders at a real url', function () {
    $this->get('/strava')
        ->assertInertia(fn ($page) => $page
            ->component('command')
            ->where('output.component', 'strava'));
});
