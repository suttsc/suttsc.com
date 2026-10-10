<?php

declare(strict_types=1);

use App\Models\Link;
use App\Terminal\Commands\LinkCommand;
use App\Terminal\Input;
use App\Terminal\Output\Window;

it('returns window output', function () {
    $link = Link::factory()->create();
    app()->instance(Input::class, new Input([(string) $link->id]));

    expect(app(LinkCommand::class)->handle())->toBeInstanceOf(Window::class);
});

it('opens a saved link in a window', function () {
    $link = Link::factory()->create([
        'url' => 'https://example.com/queues',
        'title' => 'Queues are a data structure',
        'author' => 'Someone Sensible',
        'category' => 'laravel',
        'reason' => 'Changed how I scope background work.',
    ]);

    $this->postJson(route('terminal.run'), ['command' => "/link {$link->id}"])
        ->assertOk()
        ->assertJsonPath('type', 'window')
        ->assertJsonPath('title', 'link — laravel')
        ->assertJsonPath('component', 'link')
        ->assertJsonPath('props.link', [
            'id' => $link->id,
            'url' => 'https://example.com/queues',
            'title' => 'Queues are a data structure',
            'author' => 'Someone Sensible',
            'category' => 'laravel',
            'reason' => 'Changed how I scope background work.',
            'savedOn' => $link->created_at->toDateString(),
        ]);
});

it('says so when there is no such link', function (string $command) {
    $this->postJson(route('terminal.run'), ['command' => $command])
        ->assertExactJson(['type' => 'inline', 'lines' => ['No such link. Try /links.']]);
})->with(['/link', '/link 999', '/link abc']);

it('stays out of the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => ! collect($commands)->contains('signature', '/link')
        ));
});
