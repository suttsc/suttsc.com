<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use App\Terminal\Commands\DeployCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(DeployCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('performs for the owner without deploying anything', function () {
    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => '/deploy'])
        ->assertOk()
        ->assertJsonPath('lines.5', 'None of that happened. Use git push like an adult.');

    expect(SiteStat::query()->where('key', SiteStatKey::DeployCount)->exists())->toBeFalse();
});

it('does not exist for anyone else', function () {
    $this->postJson(route('terminal.run'), ['command' => '/deploy'])
        ->assertNotFound();
});
