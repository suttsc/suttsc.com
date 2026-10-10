<?php

declare(strict_types=1);

namespace App\Integrations\PageTitle;

final class FakePageTitleFetcher implements PageTitleFetcher
{
    /**
     * @param  array<string, string>  $titles  Keyed by URL.
     */
    public function __construct(
        private array $titles = [],
    ) {}

    public function fetch(string $url): ?string
    {
        return $this->titles[$url] ?? null;
    }
}
