<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Models\Link;
use App\Terminal\Commands\LinkRemoveCommand;
use App\Terminal\Output\Inline;

it('returns inline output', function () {
    expect(app(LinkRemoveCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('lets the owner take a link off the shelf', function () {
    $link = Link::factory()->create(['title' => 'Stale take']);

    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => "/link rm {$link->id}"])
        ->assertExactJson(['type' => 'inline', 'lines' => ['Removed: Stale take']]);

    expect(Link::query()->find($link->id))->toBeNull();
});

it('explains itself when the id is missing or wrong', function () {
    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => '/link rm 42'])
        ->assertExactJson(['type' => 'inline', 'lines' => ['Usage: /link rm <id>. No link by that id.']]);
});

it('does not exist for anyone else', function () {
    $link = Link::factory()->create();

    $this->postJson(route('terminal.run'), ['command' => "/link rm {$link->id}"])
        ->assertNotFound();

    expect(Link::query()->find($link->id))->not->toBeNull();
});
