<?php

declare(strict_types=1);

use App\Terminal\Commands\CoffeeCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(CoffeeCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/coffee'])
        ->assertOk()
        ->assertJsonPath('lines.0', "418 I'm a teapot");
});

it('stays out of the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => ! collect($commands)->contains('signature', '/coffee')
        ));
});
