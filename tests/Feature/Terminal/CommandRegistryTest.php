<?php

declare(strict_types=1);

use App\Terminal\CommandRegistry;
use Illuminate\Contracts\Foundation\Application;

beforeEach(function () {
    app()->singleton(CommandRegistry::class, fn (Application $app): CommandRegistry => new CommandRegistry(
        $app,
        base_path('tests/Fixtures/Terminal/Commands'),
        'Tests\\Fixtures\\Terminal\\Commands',
    ));
});

it('ships only listed, authorised commands in the manifest, ordered by signature', function () {
    $this->get(route('home'))
        ->assertInertia(fn ($page) => $page->where('commands', [
            ['signature' => '/echo', 'description' => 'Repeats what it is given'],
            ['signature' => '/echo loud', 'description' => 'Repeats it louder'],
        ]));
});

it('runs a hidden command that the manifest leaves out', function () {
    $this->postJson(route('terminal.run'), ['command' => '/hidden'])
        ->assertOk()
        ->assertExactJson(['type' => 'inline', 'lines' => ['/hidden']]);
});

it('returns 404 for a command the visitor is not authorised to run', function () {
    $this->postJson(route('terminal.run'), ['command' => '/forbidden'])
        ->assertNotFound();
});

it('returns 404 for a command that does not exist', function () {
    $this->postJson(route('terminal.run'), ['command' => '/nope'])
        ->assertNotFound();
});

it('passes the words after the signature to the command as arguments', function () {
    $this->postJson(route('terminal.run'), ['command' => '/echo Hello  there'])
        ->assertOk()
        ->assertJsonPath('lines', ['/echo', 'Hello', 'there']);
});

it('prefers the longest matching signature', function () {
    $this->postJson(route('terminal.run'), ['command' => '/echo loud Hello'])
        ->assertOk()
        ->assertJsonPath('lines', ['/echo loud', 'Hello']);
});

it('matches a command typed without its slash or in another case', function (string $typed) {
    $this->postJson(route('terminal.run'), ['command' => $typed])
        ->assertOk()
        ->assertJsonPath('lines', ['/echo']);
})->with(['echo', '/ECHO', '  /echo  ']);

it('does not match a command that merely starts with a signature', function () {
    $this->postJson(route('terminal.run'), ['command' => '/echoes'])
        ->assertNotFound();
});

it('does not leak one run\'s arguments into the next', function () {
    $this->postJson(route('terminal.run'), ['command' => '/echo first']);

    $this->postJson(route('terminal.run'), ['command' => '/echo'])
        ->assertJsonPath('lines', ['/echo']);
});

it('returns 422 when no command is sent', function () {
    $this->postJson(route('terminal.run'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['command' => 'The command field is required.']);
});

it('returns 422 when the command is longer than 255 characters', function () {
    $this->postJson(route('terminal.run'), ['command' => '/'.str_repeat('a', 255)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['command' => 'The command field must not be greater than 255 characters.']);
});
