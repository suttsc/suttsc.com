<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class BlogCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/blog';
    }

    public function description(): string
    {
        return 'Words, eventually';
    }

    public function handle(): Output
    {
        return new Inline([
            '0 posts.',
            'There is a domain and everything.',
            '',
            'Try /links. The reasons there are the closest thing to writing so far.',
        ]);
    }
}
