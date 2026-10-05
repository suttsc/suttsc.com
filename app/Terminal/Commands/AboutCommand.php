<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Navigate;
use App\Terminal\Output\Output;

final class AboutCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/about';
    }

    public function description(): string
    {
        return 'Who you are, apparently';
    }

    public function handle(): Output
    {
        return new Navigate('about');
    }
}
