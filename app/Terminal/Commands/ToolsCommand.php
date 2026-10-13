<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Navigate;
use App\Terminal\Output\Output;

final class ToolsCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/tools';
    }

    public function description(): string
    {
        return 'Small utilities. Nothing leaves the browser';
    }

    public function handle(): Output
    {
        return new Navigate('tools.index');
    }
}
