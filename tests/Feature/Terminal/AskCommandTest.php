<?php

declare(strict_types=1);

use App\Terminal\Commands\AskCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(AskCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('repeats the question back without answering it', function () {
    $this->postJson(route('terminal.run'), ['command' => '/ask is he any good'])
        ->assertOk()
        ->assertJsonPath('lines.0', '“is he any good”');
});

it('asks what, when asked nothing', function () {
    $this->postJson(route('terminal.run'), ['command' => '/ask'])
        ->assertJsonPath('lines.0', 'Ask what?');
});

it('stays out of the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => ! collect($commands)->contains('signature', '/ask')
        ));
});
