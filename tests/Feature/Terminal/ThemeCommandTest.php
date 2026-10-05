<?php

declare(strict_types=1);

use App\Terminal\Commands\ThemeCommand;
use App\Terminal\Output\Menu;

it('returns menu output', function () {
    expect(app(ThemeCommand::class)->handle())->toBeInstanceOf(Menu::class);
});

it('runs through the palette endpoint', function () {
    $this->postJson(route('terminal.run'), ['command' => '/theme'])
        ->assertOk()
        ->assertJsonPath('type', 'menu');
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/theme')
        ));
});

it('offers each theme and marks the current one', function () {
    $this->withSession(['theme' => 'blue'])
        ->postJson(route('terminal.run'), ['command' => '/theme'])
        ->assertJsonPath('items', [
            ['label' => 'Amber phosphor', 'hint' => '', 'run' => '/theme amber'],
            ['label' => 'Green phosphor', 'hint' => '', 'run' => '/theme green'],
            ['label' => 'Cold blue', 'hint' => 'current', 'run' => '/theme blue'],
            ['label' => 'Ember', 'hint' => '', 'run' => '/theme ember'],
        ]);
});

it('remembers a chosen theme and shares it with every page', function () {
    $this->postJson(route('terminal.run'), ['command' => '/theme Green'])
        ->assertOk()
        ->assertExactJson(['type' => 'inline', 'lines' => ['Green phosphor. Noted.']])
        ->assertSessionHas('theme', 'green');

    $this->get('/')
        ->assertInertia(fn ($page) => $page->where('theme', 'green'));
});

it('does not change the theme when asked for one that does not exist', function () {
    $this->withSession(['theme' => 'ember'])
        ->postJson(route('terminal.run'), ['command' => '/theme beige'])
        ->assertOk()
        ->assertExactJson(['type' => 'inline', 'lines' => ['No phosphor by the name of beige.']])
        ->assertSessionHas('theme', 'ember');
});

it('falls back to amber when no theme has been chosen', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where('theme', 'amber'));
});
