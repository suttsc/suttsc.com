<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StravaActivity;

/**
 * Shapes the mirrored Strava runs for display. Reads the local table only.
 */
final readonly class RunningLog
{
    public function __construct(
        private SiteStats $stats,
    ) {}

    /**
     * @return array{runs: list<array{day: string, name: string, distance: string, pace: string, elevation: string}>, total: string, syncedAgo: ?string}
     */
    public function lastSevenDays(): array
    {
        $runs = StravaActivity::query()
            ->runs()
            ->where('started_at', '>=', now()->subDays(7))
            ->oldest('started_at')
            ->get();

        return [
            'runs' => array_values($runs->map(fn (StravaActivity $run): array => [
                'day' => $run->started_at->timezone(config()->string('site.timezone'))->format('D'),
                'name' => $run->name,
                'distance' => $this->kilometres($run->distance_metres),
                'pace' => $this->pace($run->paceSeconds()),
                'elevation' => "{$run->elevation_metres}m",
            ])->all()),
            'total' => $this->kilometres((int) $runs->sum('distance_metres')),
            'syncedAgo' => $this->stats->stravaSyncedAt()?->diffForHumans(),
        ];
    }

    private function kilometres(int $metres): string
    {
        return number_format($metres / 1000, 1).' km';
    }

    private function pace(?int $secondsPerKilometre): string
    {
        if ($secondsPerKilometre === null) {
            return '—';
        }

        return sprintf('%d:%02d/km', intdiv($secondsPerKilometre, 60), $secondsPerKilometre % 60);
    }
}
