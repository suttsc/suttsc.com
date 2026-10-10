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
        return $this->get(SiteStatKey::DeployCount);
    }

    public function niceTryCount(): int
    {
        return $this->get(SiteStatKey::NiceTryCount);
    }

    public function stravaSyncedAt(): ?CarbonImmutable
    {
        $timestamp = $this->get(SiteStatKey::StravaSyncedAt);

        return $timestamp === 0 ? null : CarbonImmutable::createFromTimestamp($timestamp);
    }

    public function increment(SiteStatKey $key): void
    {
        SiteStat::query()->firstOrCreate(['key' => $key], ['value' => 0])->increment('value');
    }

    public function set(SiteStatKey $key, int $value): void
    {
        SiteStat::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    private function get(SiteStatKey $key): int
    {
        return (int) SiteStat::query()->where('key', $key)->value('value');
    }
}
