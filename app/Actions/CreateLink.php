<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\LinkCategory;
use App\Jobs\FetchLinkTitle;
use App\Models\Link;

final class CreateLink
{
    /**
     * Shelve a link. Its title arrives later, from a queued job.
     */
    public function handle(string $url, LinkCategory $category, string $reason, ?string $author = null): Link
    {
        $link = Link::query()->create([
            'url' => $url,
            'category' => $category,
            'reason' => $reason,
            'author' => $author,
        ]);

        FetchLinkTitle::dispatch($link);

        return $link;
    }
}
