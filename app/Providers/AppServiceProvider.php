<?php

namespace App\Providers;

use App\Auth\OwnerUserProvider;
use App\Integrations\PageTitle\HttpPageTitleFetcher;
use App\Integrations\PageTitle\PageTitleFetcher;
use App\Integrations\Strava\HttpStravaClient;
use App\Integrations\Strava\StravaClient;
use App\Terminal\CommandRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Passkeys;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();
        Passkeys::ignoreRoutes();

        $this->app->singleton(CommandRegistry::class, fn (Application $app): CommandRegistry => new CommandRegistry(
            $app,
            app_path('Terminal/Commands'),
            'App\\Terminal\\Commands',
        ));

        $this->app->bind(PageTitleFetcher::class, HttpPageTitleFetcher::class);

        $this->app->bind(StravaClient::class, fn (Application $app): StravaClient => new HttpStravaClient(
            $app->make(HttpFactory::class),
            $app->make('cache.store'),
            config()->string('services.strava.client_id'),
            config()->string('services.strava.client_secret'),
            config()->string('services.strava.refresh_token'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Auth::provider('owner', fn (): OwnerUserProvider => new OwnerUserProvider);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }
}
