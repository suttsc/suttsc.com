<?php

declare(strict_types=1);

use App\Integrations\PageTitle\FakePageTitleFetcher;
use App\Integrations\PageTitle\PageTitleFetcher;
use App\Jobs\FetchLinkTitle;
use App\Models\Link;

it('fills in the title from the page', function () {
    app()->instance(PageTitleFetcher::class, new FakePageTitleFetcher([
        'https://example.com/post' => 'A Post Worth Reading',
    ]));
    $link = Link::factory()->untitled()->create(['url' => 'https://example.com/post']);

    FetchLinkTitle::dispatchSync($link);

    expect($link->fresh()->title)->toBe('A Post Worth Reading');
});

it('leaves the title empty when the page has none', function () {
    app()->instance(PageTitleFetcher::class, new FakePageTitleFetcher);
    $link = Link::factory()->untitled()->create();

    FetchLinkTitle::dispatchSync($link);

    expect($link->fresh()->title)->toBeNull();
});

it('does not overwrite a title that is already there', function () {
    app()->instance(PageTitleFetcher::class, new FakePageTitleFetcher([
        'https://example.com/post' => 'Something Else',
    ]));
    $link = Link::factory()->create(['url' => 'https://example.com/post', 'title' => 'Kept']);

    FetchLinkTitle::dispatchSync($link);

    expect($link->fresh()->title)->toBe('Kept');
});
