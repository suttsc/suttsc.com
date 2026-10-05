<?php

declare(strict_types=1);

namespace Tests\Fixtures\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class EchoLoudCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/echo loud';
    }

    public function description(): string
    {
        return 'Repeats it louder';
    }

    public function handle(): Output
    {
        return new Inline(['/echo loud', ...$this->input->arguments]);
    }
}
