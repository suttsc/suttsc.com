<?php

declare(strict_types=1);

namespace App\Integrations\Strava;

interface StravaClient
{
    /**
     * The owner's activities since the given moment, newest first.
     *
     * @return list<StravaActivityData>
     */
    public function activitiesSince(\DateTimeInterface $since): array;
}
