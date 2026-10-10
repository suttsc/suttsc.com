<?php

declare(strict_types=1);

use App\Terminal\Commands\BlogCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(BlogCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/blog'])
        ->assertOk()
        ->assertJsonPath('lines.0', '0 posts.');
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/blog')
        ));
});

it('renders at a real url', function () {
    $this->get('/blog')
        ->assertInertia(fn ($page) => $page
            ->component('command')
            ->where('signature', '/blog')
            ->where('output.type', 'inline'));
});
