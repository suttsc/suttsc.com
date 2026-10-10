<?php

declare(strict_types=1);

namespace App\Enums;

enum SiteStatKey: string
{
    case DeployCount = 'deploy_count';
    case NiceTryCount = 'nice_try_count';
    case StravaSyncedAt = 'strava_synced_at';
}
