<?php

declare(strict_types=1);

namespace Tests\Fixtures\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class EchoCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/echo';
    }

    public function description(): string
    {
        return 'Repeats what it is given';
    }

    public function handle(): Output
    {
        return new Inline(['/echo', ...$this->input->arguments]);
    }
}
