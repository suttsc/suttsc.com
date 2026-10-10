<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;
use App\Terminal\Output\Window;

/**
 * Opens one saved link. Reached from the /links menu rather than typed.
 */
final class LinkCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/link';
    }

    public function description(): string
    {
        return 'Open a saved link';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function handle(): Output
    {
        $id = $this->input->argument(0);
        $link = ctype_digit((string) $id) ? Link::query()->find((int) $id) : null;

        if ($link === null) {
            return new Inline(['No such link. Try /links.']);
        }

        return new Window(
            "link — {$link->category->value}",
            'link',
            ['link' => (new LinkResource($link))->resolve()],
        );
    }
}
