<?php

declare(strict_types=1);

namespace Tests\Fixtures\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class ForbiddenCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/forbidden';
    }

    public function description(): string
    {
        return 'Not for you';
    }

    public function authorized(): bool
    {
        return false;
    }

    public function handle(): Output
    {
        return new Inline(['/forbidden', ...$this->input->arguments]);
    }
}
