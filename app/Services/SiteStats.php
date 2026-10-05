<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use Carbon\CarbonImmutable;

final class SiteStats
{
    public function domainAgeInDays(): int
    {
        $registeredAt = CarbonImmutable::parse(config()->string('site.registered_at'));

        return (int) $registeredAt->diffInDays(now());
    }

    public function deployCount(): int
    {
        return (int) SiteStat::query()->where('key', SiteStatKey::DeployCount)->value('value');
    }
}
