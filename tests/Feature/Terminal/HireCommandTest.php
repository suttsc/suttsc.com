<?php

declare(strict_types=1);

use App\Terminal\Commands\HireCommand;
use App\Terminal\Output\Navigate;

it('returns navigate output', function () {
    expect(app(HireCommand::class)->handle())->toBeInstanceOf(Navigate::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/hire'])
        ->assertOk()
        ->assertExactJson(['type' => 'navigate', 'url' => '/hire']);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/hire')
        ));
});

it('renders at a real url', function () {
    $this->get('/hire')
        ->assertInertia(fn ($page) => $page->component('hire'));
});
