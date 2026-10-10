<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Enums\LinkCategory;
use App\Jobs\FetchLinkTitle;
use App\Models\Link;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

it('shelves a link for the owner and queues the title fetch', function () {
    $this->actingAs(new Owner)
        ->postJson(route('links.store'), [
            'url' => 'https://example.com/post',
            'category' => 'architecture',
            'reason' => 'Still thinking about it.',
            'author' => 'A. Writer',
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'https://example.com/post');

    $link = Link::query()->sole();

    expect($link)
        ->url->toBe('https://example.com/post')
        ->title->toBeNull()
        ->author->toBe('A. Writer')
        ->category->toBe(LinkCategory::Architecture)
        ->reason->toBe('Still thinking about it.');

    Queue::assertPushed(FetchLinkTitle::class, fn (FetchLinkTitle $job): bool => $job->link->is($link));
});

it('refuses anyone but the owner', function () {
    $this->postJson(route('links.store'), [
        'url' => 'https://example.com/post',
        'category' => 'architecture',
        'reason' => 'Sneaky.',
    ])->assertUnauthorized();

    expect(Link::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

it('validates what it is given', function (array $input, string $field) {
    $this->actingAs(new Owner)
        ->postJson(route('links.store'), [
            'url' => 'https://example.com/post',
            'category' => 'architecture',
            'reason' => 'Fine.',
            ...$input,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'missing url' => [['url' => ''], 'url'],
    'not a url' => [['url' => 'nonsense'], 'url'],
    'not http' => [['url' => 'javascript:alert(1)'], 'url'],
    'unknown category' => [['category' => 'gossip'], 'category'],
    'missing reason' => [['reason' => ''], 'reason'],
]);
