<?php

declare(strict_types=1);

use App\Terminal\Commands\HelpCommand;
use App\Terminal\Output\Menu;

it('returns menu output', function () {
    expect(app(HelpCommand::class)->handle())->toBeInstanceOf(Menu::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/help'])
        ->assertOk()
        ->assertJsonPath('type', 'menu');
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/help')
        ));
});

it('lists every command in the manifest, itself included', function () {
    $this->postJson(route('terminal.run'), ['command' => '/help'])
        ->assertJsonPath('items.*.run', [
            '/about', '/blog', '/help', '/hire', '/links', '/projects', '/strava', '/sudo', '/theme', '/tools', '/uptime', '/whoami',
        ])
        ->assertJsonPath('items.2', [
            'label' => '/help',
            'hint' => 'Everything this machine admits to',
            'run' => '/help',
        ]);
});

it('renders at a real url', function () {
    $this->get('/help')
        ->assertInertia(fn ($page) => $page
            ->component('command')
            ->where('signature', '/help')
            ->where('output.type', 'menu'));
});
