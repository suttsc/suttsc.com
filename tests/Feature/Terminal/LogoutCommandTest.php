<?php

declare(strict_types=1);

use App\Auth\Owner;

it('signs the owner out', function () {
    $this->actingAs(new Owner)
        ->postJson(route('terminal.run'), ['command' => '/logout'])
        ->assertExactJson(['type' => 'inline', 'lines' => ['Signed out. You are suttsc again, unofficially.']]);

    $this->assertGuest();
});

it('appears in the manifest only for the owner', function () {
    $this->actingAs(new Owner)
        ->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/logout')
        ));
});

it('does not exist for anyone else', function () {
    $this->postJson(route('terminal.run'), ['command' => '/logout'])
        ->assertNotFound();
});
