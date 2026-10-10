<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Output;
use App\Terminal\Output\Window;

/**
 * The window reads the browser and timezone itself, at render time. Nothing
 * about the visitor reaches the server, let alone stays there.
 */
final class WhoamiCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/whoami';
    }

    public function description(): string
    {
        return 'Identify the current user';
    }

    public function handle(): Output
    {
        return new Window('whoami', 'whoami');
    }
}
