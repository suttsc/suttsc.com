<?php

declare(strict_types=1);

use App\Models\Link;

it('lists every link at a real url', function () {
    Link::factory()->count(2)->create();

    $this->get(route('links.index'))
        ->assertInertia(fn ($page) => $page
            ->component('links/index')
            ->has('links', 2));
});

it('shows one link at its own url', function () {
    $link = Link::factory()->create(['title' => 'Worth it']);

    $this->get(route('links.show', $link))
        ->assertInertia(fn ($page) => $page
            ->component('links/show')
            ->where('link.title', 'Worth it'));
});

it('404s for a link that does not exist', function () {
    $this->get('/links/999')->assertNotFound();
});
