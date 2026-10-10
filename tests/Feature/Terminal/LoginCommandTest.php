<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Terminal\Commands\LoginCommand;
use App\Terminal\Output\Navigate;

it('returns navigate output', function () {
    expect(app(LoginCommand::class)->handle())->toBeInstanceOf(Navigate::class);
});

it('sends a visitor to google', function () {
    $this->postJson(route('terminal.run'), ['command' => '/login'])
        ->assertExactJson(['type' => 'navigate', 'url' => '/auth/google']);
});

it('tells the owner they are already in', function () {
    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => '/login'])
        ->assertExactJson(['type' => 'inline', 'lines' => ['Already signed in. Still suttsc.']]);
});

it('stays out of the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => ! collect($commands)->contains('signature', '/login')
        ));
});
