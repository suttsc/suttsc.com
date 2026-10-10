<?php

declare(strict_types=1);

namespace App\Integrations\Strava;

use DateTimeInterface;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Client\Factory as Http;

/**
 * Talks to Strava from scheduled jobs only. Never resolve this in a request.
 */
final readonly class HttpStravaClient implements StravaClient
{
    /**
     * Strava rotates the refresh token, so the latest one outlives config.
     */
    private const string RefreshTokenKey = 'strava.refresh_token';

    public function __construct(
        private Http $http,
        private Cache $cache,
        private string $clientId,
        private string $clientSecret,
        private string $refreshToken,
    ) {}

    public function activitiesSince(DateTimeInterface $since): array
    {
        /** @var list<array{id: int, name: string, sport_type: string, distance: float|int, moving_time: int, total_elevation_gain: float|int, start_date: string}> $activities */
        $activities = $this->http
            ->withToken($this->accessToken())
            ->timeout(10)
            ->get('https://www.strava.com/api/v3/athlete/activities', [
                'after' => $since->getTimestamp(),
                'per_page' => 100,
            ])
            ->throw()
            ->json();

        return array_map(StravaActivityData::fromApi(...), $activities);
    }

    private function accessToken(): string
    {
        $response = $this->http
            ->asForm()
            ->timeout(10)
            ->post('https://www.strava.com/oauth/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->cache->get(self::RefreshTokenKey, $this->refreshToken),
            ])
            ->throw();

        $this->cache->forever(self::RefreshTokenKey, $response->json('refresh_token'));

        return (string) $response->json('access_token');
    }
}
