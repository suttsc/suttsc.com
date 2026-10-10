<?php

declare(strict_types=1);

use App\Auth\Owner;
use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;

beforeEach(function () {
    config(['auth.owner_email' => 'chris@example.com']);
});

function googleUser(string $email, bool $verified = true): GoogleUser
{
    return GoogleUser::fake([
        'name' => 'Some Person',
        'email' => $email,
        'email_verified' => $verified,
    ]);
}

it('hands the visitor to google', function () {
    Socialite::fake('google');

    $this->get(route('auth.google.redirect'))
        ->assertRedirect('https://socialite.fake/google/authorize');
});

it('tells inertia to leave the app for google', function () {
    Socialite::fake('google');

    $this->get(route('auth.google.redirect'), ['X-Inertia' => 'true'])
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', 'https://socialite.fake/google/authorize');
});

it('signs the owner in', function (string $email) {
    Socialite::fake('google', googleUser($email));

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs(new Owner);
})->with(['chris@example.com', ' Chris@Example.com ']);

it('turns away anyone else and counts it', function (string $email) {
    Socialite::fake('google', googleUser($email));

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect(SiteStat::query()->find(SiteStatKey::NiceTryCount->value)?->value)->toBe(1);
})->with([
    'a stranger' => 'someone@example.com',
    'a lookalike on the same domain' => 'chris2@example.com',
    'a superstring' => 'chris@example.com.evil.test',
]);

it('shows the rejected visitor their own details once', function () {
    Socialite::fake('google', googleUser('someone@example.com'));

    $this->followingRedirects()
        ->get(route('auth.google.callback'))
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->hasFlash('rejection', ['name' => 'Some Person', 'email' => 'someone@example.com']));

    $this->get(route('home'))
        ->assertInertia(fn ($page) => $page->missingFlash('rejection'));
});

it('refuses an unverified google address even when it matches', function () {
    Socialite::fake('google', googleUser('chris@example.com', verified: false));

    $this->get(route('auth.google.callback'));

    $this->assertGuest();
});

it('refuses everyone when no owner is configured', function () {
    config(['auth.owner_email' => null]);
    Socialite::fake('google', googleUser(''));

    $this->get(route('auth.google.callback'));

    $this->assertGuest();
});

it('goes home quietly when the state does not match', function () {
    Socialite::fake('google', fn () => throw new InvalidStateException);

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

it('restores the owner from the session without a users table', function () {
    $this->withSession([Auth::guard()->getName() => Owner::Identifier])
        ->get(route('home'))
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/logout')
        ));
});

it('restores nobody from a session holding any other identifier', function () {
    $this->withSession([Auth::guard()->getName() => 'someone'])
        ->get(route('home'));

    $this->assertGuest();
});
