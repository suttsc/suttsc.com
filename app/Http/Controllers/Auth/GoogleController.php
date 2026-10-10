<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Auth\Owner;
use App\Auth\OwnerEmail;
use App\Enums\SiteStatKey;
use App\Http\Controllers\Controller;
use App\Services\SiteStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\Response;

final class GoogleController extends Controller
{
    /**
     * Send the visitor to Google. Stateful, so the state parameter guards the callback.
     */
    public function redirect(): Response
    {
        return Inertia::location(Socialite::driver('google')->redirect()->getTargetUrl());
    }

    /**
     * Let the owner in. Anyone else sees their own details for one render, then
     * they are gone; only the count survives.
     */
    public function callback(Request $request, SiteStats $stats): RedirectResponse
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return to_route('home');
        }

        $verified = $google instanceof AbstractUser && ($google->getRaw()['email_verified'] ?? false) === true;

        if (! $verified || ! OwnerEmail::matches($google->getEmail())) {
            $stats->increment(SiteStatKey::NiceTryCount);

            Inertia::flash('rejection', [
                'name' => $google->getName(),
                'email' => $google->getEmail(),
            ]);

            return to_route('home');
        }

        Auth::login(new Owner);
        $request->session()->regenerate();

        Inertia::flash('signedIn', true);

        return to_route('home');
    }
}
