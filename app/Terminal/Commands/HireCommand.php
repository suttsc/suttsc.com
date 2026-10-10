<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Navigate;
use App\Terminal\Output\Output;

final class HireCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/hire';
    }

    public function description(): string
    {
        return 'Availability and how to get in touch';
    }

    public function handle(): Output
    {
        return new Navigate('hire');
    }
}
