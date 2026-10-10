<?php

declare(strict_types=1);

namespace App\Integrations\PageTitle;

interface PageTitleFetcher
{
    /**
     * The contents of the page's <title>, or null when there isn't a usable one.
     */
    public function fetch(string $url): ?string;
}
