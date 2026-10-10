<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Models\Link;
use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Menu;
use App\Terminal\Output\MenuItem;
use App\Terminal\Output\Output;

final class LinksCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/links';
    }

    public function description(): string
    {
        return 'Things other people wrote, still being thought about';
    }

    public function handle(): Output
    {
        $links = Link::query()->latest()->latest('id')->get();

        if ($links->isEmpty()) {
            return new Inline(['The shelf is empty. Reading is happening, allegedly.']);
        }

        return new Menu('Links', array_values($links->map(fn (Link $link): MenuItem => new MenuItem(
            $link->heading(),
            $link->category->value,
            "/link {$link->id}",
        ))->all()));
    }
}
