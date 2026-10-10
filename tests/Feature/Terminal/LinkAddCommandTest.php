<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Terminal\Commands\LinkAddCommand;
use App\Terminal\Output\Window;

it('returns window output', function () {
    expect(app(LinkAddCommand::class)->handle())->toBeInstanceOf(Window::class);
});

it('opens the new link form for the owner', function () {
    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => '/link add'])
        ->assertOk()
        ->assertJsonPath('component', 'link-add')
        ->assertJsonPath('props.categories', ['architecture', 'laravel', 'leadership', 'design', 'testing', 'other']);
});

it('appears in the manifest for the owner', function () {
    $this->actingAs(new Owner)
        ->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/link add')
        ));
});

it('does not exist for anyone else', function () {
    $this->postJson(route('terminal.run'), ['command' => '/link add'])
        ->assertNotFound();

    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => ! collect($commands)->contains('signature', '/link add')
        ));
});
