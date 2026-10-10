<?php

declare(strict_types=1);

use App\Models\Link;
use App\Terminal\Commands\LinksCommand;
use App\Terminal\Output\Menu;

it('returns menu output', function () {
    Link::factory()->create();

    expect(app(LinksCommand::class)->handle())->toBeInstanceOf(Menu::class);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/links')
        ));
});

it('lists links newest first, each opening its own window', function () {
    $older = Link::factory()->create(['title' => 'Older', 'created_at' => now()->subDay()]);
    $newer = Link::factory()->create(['title' => 'Newer']);

    $this->postJson(route('terminal.run'), ['command' => '/links'])
        ->assertOk()
        ->assertJsonPath('items.*.label', ['Newer', 'Older'])
        ->assertJsonPath('items.*.run', ["/link {$newer->id}", "/link {$older->id}"]);
});

it('shows the address while the title is still being fetched', function () {
    Link::factory()->untitled()->create(['url' => 'https://example.com/post']);

    $this->postJson(route('terminal.run'), ['command' => '/links'])
        ->assertJsonPath('items.0.label', 'https://example.com/post');
});

it('admits to an empty shelf', function () {
    $this->postJson(route('terminal.run'), ['command' => '/links'])
        ->assertExactJson(['type' => 'inline', 'lines' => ['The shelf is empty. Reading is happening, allegedly.']]);
});
