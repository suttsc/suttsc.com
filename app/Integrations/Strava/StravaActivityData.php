<?php

declare(strict_types=1);

namespace App\Integrations\Strava;

use Carbon\CarbonImmutable;

final readonly class StravaActivityData
{
    public function __construct(
        public int $stravaId,
        public string $name,
        public string $sportType,
        public int $distanceMetres,
        public int $movingSeconds,
        public int $elevationMetres,
        public CarbonImmutable $startedAt,
    ) {}

    /**
     * @param  array{id: int, name: string, sport_type: string, distance: float|int, moving_time: int, total_elevation_gain: float|int, start_date: string}  $activity
     */
    public static function fromApi(array $activity): self
    {
        return new self(
            stravaId: $activity['id'],
            name: $activity['name'],
            sportType: $activity['sport_type'],
            distanceMetres: (int) round($activity['distance']),
            movingSeconds: $activity['moving_time'],
            elevationMetres: (int) round($activity['total_elevation_gain']),
            startedAt: CarbonImmutable::parse($activity['start_date']),
        );
    }
}
