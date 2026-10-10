<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Integrations\PageTitle\PageTitleFetcher;
use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class FetchLinkTitle implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Link $link,
    ) {}

    public function handle(PageTitleFetcher $fetcher): void
    {
        if ($this->link->title !== null) {
            return;
        }

        $title = $fetcher->fetch($this->link->url);

        if ($title !== null) {
            $this->link->update(['title' => $title]);
        }
    }
}
