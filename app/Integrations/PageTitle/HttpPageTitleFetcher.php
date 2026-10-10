<?php

declare(strict_types=1);

namespace App\Integrations\PageTitle;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\Str;

/**
 * Fetches a saved link's <title> from a queued job, never from a request.
 */
final readonly class HttpPageTitleFetcher implements PageTitleFetcher
{
    public function __construct(
        private Http $http,
    ) {}

    public function fetch(string $url): ?string
    {
        try {
            $response = $this->http
                ->timeout(10)
                ->withUserAgent('suttsc.com link shelf')
                ->get($url);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $response->body(), $matches) !== 1) {
            return null;
        }

        $title = Str::of(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5))->squish()->limit(255, '');

        return $title->isEmpty() ? null : $title->value();
    }
}
