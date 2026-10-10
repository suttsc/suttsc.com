<?php

declare(strict_types=1);

use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use App\Terminal\Commands\UptimeCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(UptimeCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/uptime'])
        ->assertOk()
        ->assertJsonPath('type', 'inline');
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/uptime')
        ));
});

it('reports the days since registration and the deploy count', function () {
    config(['site.registered_at' => '2026-01-01']);
    $this->travelTo('2026-01-11 09:30:00');
    SiteStat::factory()->create(['key' => SiteStatKey::DeployCount, 'value' => 7]);

    $this->postJson(route('terminal.run'), ['command' => '/uptime'])
        ->assertJsonPath('lines.0', 'domain registered          10 days ago')
        ->assertJsonPath('lines.1', 'deploys so far             7');
});

it('reports zero deploys before the first one is recorded', function () {
    $this->postJson(route('terminal.run'), ['command' => '/uptime'])
        ->assertJsonPath('lines.1', 'deploys so far             0');
});

it('renders at a real url', function () {
    $this->get('/uptime')
        ->assertInertia(fn ($page) => $page
            ->component('command')
            ->where('signature', '/uptime')
            ->where('output.type', 'inline'));
});

it('counts the failed impersonations', function () {
    SiteStat::factory()->create(['key' => SiteStatKey::NiceTryCount, 'value' => 3]);

    $this->postJson(route('terminal.run'), ['command' => '/uptime'])
        ->assertJsonPath('lines.3', 'impersonation attempts     3');
});
