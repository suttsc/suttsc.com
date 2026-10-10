<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class CoffeeCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/coffee';
    }

    public function description(): string
    {
        return 'Brew coffee';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function handle(): Output
    {
        return new Inline([
            "418 I'm a teapot",
            'This server is, permanently, a teapot.',
        ]);
    }
}
