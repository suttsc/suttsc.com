<?php

declare(strict_types=1);

namespace App\Terminal;

use App\Terminal\Contracts\TerminalCommand;

abstract class BaseCommand implements TerminalCommand
{
    public function hidden(): bool
    {
        return false;
    }

    public function authorized(): bool
    {
        return true;
    }
}
