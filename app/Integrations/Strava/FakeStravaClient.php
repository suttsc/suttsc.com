<?php

declare(strict_types=1);

namespace App\Integrations\Strava;

use DateTimeInterface;

final class FakeStravaClient implements StravaClient
{
    /**
     * @param  list<StravaActivityData>  $activities
     */
    public function __construct(
        private array $activities = [],
    ) {}

    public function activitiesSince(DateTimeInterface $since): array
    {
        return array_values(array_filter(
            $this->activities,
            fn (StravaActivityData $activity): bool => $activity->startedAt->getTimestamp() > $since->getTimestamp(),
        ));
    }
}
