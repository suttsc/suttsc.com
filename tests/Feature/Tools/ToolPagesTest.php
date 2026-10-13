<?php

declare(strict_types=1);

use App\Enums\Tool;
use App\Http\Middleware\HandleInertiaRequests;

it('lays out every tool as a desktop icon', function () {
    $this->get(route('tools.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tools/index')
            ->has('tools', count(Tool::cases()))
            ->where('tools.0', [
                'slug' => 'base64',
                'label' => 'base64',
                'description' => 'Encode and decode, URL-safe if asked',
                'glyph' => '64',
                'href' => null,
                'component' => 'tool-base64',
                'url' => '/tools/base64',
            ]));
});

it('gives each window tool its own page', function (Tool $tool) {
    $this->get(route('tools.show', $tool))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tools/show')
            ->where('tool.component', $tool->component()));
})->with(Tool::cases());

it('puts configured links on the desktop alongside the tools', function () {
    config(['site.tool_links' => [
        ['label' => 'Regex 101', 'description' => 'Somebody else\'s tool', 'glyph' => '.*', 'url' => 'https://regex101.com'],
    ]]);

    $this->get(route('tools.index'))
        ->assertInertia(fn ($page) => $page
            ->has('tools', count(Tool::cases()) + 1)
            ->where('tools.'.count(Tool::cases()), [
                'slug' => 'regex-101',
                'label' => 'Regex 101',
                'description' => 'Somebody else\'s tool',
                'glyph' => '.*',
                'href' => 'https://regex101.com',
                'component' => null,
                'url' => 'https://regex101.com',
            ]));
});

it('404s for a tool that does not exist', function () {
    $this->get('/tools/nope')->assertNotFound();
});

it('sets no cookies at all', function (string $url) {
    $response = $this->get($url)->assertOk();

    expect($response->headers->getCookies())->toBe([]);
})->with(['/tools', '/tools/base64']);

it('sets no cookies on an inertia visit either', function () {
    $response = $this->get('/tools', [
        'X-Inertia' => 'true',
        'X-Inertia-Version' => (string) app(HandleInertiaRequests::class)->version(request()),
    ])->assertOk();

    expect($response->headers->getCookies())->toBe([]);
});

it('asks crawlers not to index it', function (string $url) {
    $this->get($url)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
})->with(['/tools', '/tools/base64']);

it('leaves the theme to the client, having no session to read it from', function () {
    $this->get('/tools')
        ->assertInertia(fn ($page) => $page->where('theme', null));
});

it('still sets the usual cookies everywhere else', function () {
    expect($this->get('/')->headers->getCookies())->not->toBe([]);
});
