<?php

declare(strict_types=1);

use App\Terminal\Commands\ToolsCommand;
use App\Terminal\Output\Navigate;

it('returns navigate output', function () {
    expect(app(ToolsCommand::class)->handle())->toBeInstanceOf(Navigate::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/tools'])
        ->assertOk()
        ->assertExactJson(['type' => 'navigate', 'url' => '/tools']);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/tools')
        ));
});
