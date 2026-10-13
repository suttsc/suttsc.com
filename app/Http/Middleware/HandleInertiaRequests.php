<?php

namespace App\Http\Middleware;

use App\Enums\Theme;
use App\Terminal\CommandRegistry;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(
        private readonly CommandRegistry $registry,
    ) {}

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'commands' => $this->registry->manifest(),
            // Cookieless pages have no session to read a theme from; the client keeps the one it has.
            'theme' => $request->hasSession() ? Theme::current($request->session())->value : null,
            'ownerTimezone' => config('site.timezone'),
        ];
    }
}
