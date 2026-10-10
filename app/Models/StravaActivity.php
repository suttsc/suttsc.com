<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\StravaActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A local mirror of one Strava activity. Requests read this, never Strava.
 *
 * @property int $id
 * @property int $strava_id
 * @property string $name
 * @property string $sport_type
 * @property int $distance_metres
 * @property int $moving_seconds
 * @property int $elevation_metres
 * @property CarbonImmutable $started_at
 */
final class StravaActivity extends Model
{
    /** @use HasFactory<StravaActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'strava_id',
        'name',
        'sport_type',
        'distance_metres',
        'moving_seconds',
        'elevation_metres',
        'started_at',
    ];

    /**
     * @param  Builder<self>  $query
     */
    public function scopeRuns(Builder $query): void
    {
        $query->whereIn('sport_type', ['Run', 'TrailRun', 'VirtualRun']);
    }

    /**
     * Seconds per kilometre, or null for a run that went nowhere.
     */
    public function paceSeconds(): ?int
    {
        if ($this->distance_metres === 0) {
            return null;
        }

        return (int) round($this->moving_seconds / ($this->distance_metres / 1000));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'strava_id' => 'integer',
            'distance_metres' => 'integer',
            'moving_seconds' => 'integer',
            'elevation_metres' => 'integer',
            'started_at' => 'immutable_datetime',
        ];
    }
}
