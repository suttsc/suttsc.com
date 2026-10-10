<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Navigate;
use App\Terminal\Output\Output;

final class ProjectsCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/projects';
    }

    public function description(): string
    {
        return 'Things built on purpose';
    }

    public function handle(): Output
    {
        return new Navigate('projects');
    }
}
