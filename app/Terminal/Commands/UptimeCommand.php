<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Services\SiteStats;
use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

final class UptimeCommand extends BaseCommand
{
    public function __construct(
        private readonly SiteStats $stats,
    ) {}

    public function signature(): string
    {
        return '/uptime';
    }

    public function description(): string
    {
        return 'Statistics of questionable value';
    }

    public function handle(): Output
    {
        return new Inline([
            sprintf('domain registered          %d days ago', $this->stats->domainAgeInDays()),
            sprintf('deploys so far             %d', $this->stats->deployCount()),
            'blog posts written         0',
            sprintf('impersonation attempts     %d', $this->stats->niceTryCount()),
            'consecutive days without',
            '  touching the CSS         0',
        ]);
    }
}
