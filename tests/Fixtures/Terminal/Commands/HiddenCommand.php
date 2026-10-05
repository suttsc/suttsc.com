<?php

declare(strict_types=1);

namespace Tests\Fixtures\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class HiddenCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/hidden';
    }

    public function description(): string
    {
        return 'Works but is not listed';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function handle(): Output
    {
        return new Inline(['/hidden', ...$this->input->arguments]);
    }
}
