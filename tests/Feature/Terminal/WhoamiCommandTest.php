<?php

declare(strict_types=1);

use App\Terminal\Commands\WhoamiCommand;
use App\Terminal\Output\Window;

it('returns window output', function () {
    expect(app(WhoamiCommand::class)->handle())->toBeInstanceOf(Window::class);
});

it('runs through the palette endpoint without sending anything about the visitor', function () {
    $this->postJson(route('terminal.run'), ['command' => '/whoami'])
        ->assertOk()
        ->assertExactJson(['type' => 'window', 'title' => 'whoami', 'component' => 'whoami', 'props' => []]);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/whoami')
        ));
});

it('renders at a real url', function () {
    $this->get('/whoami')
        ->assertInertia(fn ($page) => $page
            ->component('command')
            ->where('output.component', 'whoami'));
});
