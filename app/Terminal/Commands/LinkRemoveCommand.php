<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Models\Link;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;
use App\Terminal\OwnerCommand;

final class LinkRemoveCommand extends OwnerCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/link rm';
    }

    public function description(): string
    {
        return 'Take a link off the shelf, by id';
    }

    public function handle(): Output
    {
        $id = $this->input->argument(0);
        $link = ctype_digit((string) $id) ? Link::query()->find((int) $id) : null;

        if ($link === null) {
            return new Inline(['Usage: /link rm <id>. No link by that id.']);
        }

        $link->delete();

        return new Inline(["Removed: {$link->heading()}"]);
    }
}
