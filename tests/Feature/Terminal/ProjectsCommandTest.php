<?php

declare(strict_types=1);

use App\Terminal\Commands\ProjectsCommand;
use App\Terminal\Output\Navigate;

it('returns navigate output', function () {
    expect(app(ProjectsCommand::class)->handle())->toBeInstanceOf(Navigate::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/projects'])
        ->assertOk()
        ->assertExactJson(['type' => 'navigate', 'url' => '/projects']);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/projects')
        ));
});

it('renders the configured projects at a real url', function () {
    config(['site.projects' => [
        ['name' => 'Thing', 'summary' => 'A thing.', 'stack' => 'php', 'url' => null],
    ]]);

    $this->get('/projects')
        ->assertInertia(fn ($page) => $page
            ->component('projects')
            ->where('projects.0.name', 'Thing'));
});
