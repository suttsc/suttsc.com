<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Services\RunningLog;
use App\Terminal\BaseCommand;
use App\Terminal\Output\Output;
use App\Terminal\Output\Window;

final class StravaCommand extends BaseCommand
{
    public function __construct(
        private readonly RunningLog $log,
    ) {}

    public function signature(): string
    {
        return '/strava';
    }

    public function description(): string
    {
        return 'Recent runs, whether you asked or not';
    }

    public function handle(): Output
    {
        return new Window('strava — last 7 days', 'strava', $this->log->lastSevenDays());
    }
}
