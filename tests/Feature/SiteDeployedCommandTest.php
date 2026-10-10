<?php

declare(strict_types=1);

use App\Enums\SiteStatKey;
use App\Models\SiteStat;

it('counts a deploy for /uptime', function () {
    $this->artisan('site:deployed')->expectsOutput('Deploy #1 recorded.')->assertSuccessful();
    $this->artisan('site:deployed')->expectsOutput('Deploy #2 recorded.')->assertSuccessful();

    expect(SiteStat::query()->find(SiteStatKey::DeployCount->value)?->value)->toBe(2);
});
