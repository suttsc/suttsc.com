<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;
use App\Terminal\OwnerCommand;

/**
 * Theatre. A web request has no business starting a deploy.
 */
final class DeployCommand extends OwnerCommand
{
    public function signature(): string
    {
        return '/deploy';
    }

    public function description(): string
    {
        return 'Ship it';
    }

    public function handle(): Output
    {
        return new Inline([
            'pushing to main…',
            'build ok',
            'migrations 0 pending',
            'deployed in 34s',
            '',
            'None of that happened. Use git push like an adult.',
        ]);
    }
}
