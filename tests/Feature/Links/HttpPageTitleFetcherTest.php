<?php

declare(strict_types=1);

use App\Integrations\PageTitle\HttpPageTitleFetcher;
use Illuminate\Support\Facades\Http;

it('reads and tidies the page title', function () {
    Http::fake(['example.com/*' => Http::response("<html><head><title>\n  Queues &amp; you  </title></head></html>")]);

    expect(app(HttpPageTitleFetcher::class)->fetch('https://example.com/post'))->toBe('Queues & you');
});

it('returns null for a failed response or a page without a title', function (mixed $response) {
    Http::fake(['example.com/*' => $response]);

    expect(app(HttpPageTitleFetcher::class)->fetch('https://example.com/post'))->toBeNull();
})->with([
    'server error' => fn () => Http::response('<title>Error</title>', 500),
    'no title' => fn () => Http::response('<html></html>'),
    'empty title' => fn () => Http::response('<title>   </title>'),
]);
