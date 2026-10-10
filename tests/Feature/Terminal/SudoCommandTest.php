<?php

declare(strict_types=1);

use App\Terminal\Commands\SudoCommand;
use App\Terminal\Output\Inline;

it('returns inline output on the first attempt', function () {
    expect(app(SudoCommand::class)->handle())->toBeInstanceOf(Inline::class);
});

it('appears in the manifest', function () {
    $this->get('/')
        ->assertInertia(fn ($page) => $page->where(
            'commands',
            fn ($commands) => collect($commands)->contains('signature', '/sudo')
        ));
});

it('escalates from a warning to an incident report', function () {
    $this->postJson(route('terminal.run'), ['command' => '/sudo'])
        ->assertExactJson(['type' => 'inline', 'lines' => [
            'suttsc is not in the sudoers file.',
            'This incident will be reported.',
        ]]);

    $this->postJson(route('terminal.run'), ['command' => '/sudo'])
        ->assertExactJson(['type' => 'inline', 'lines' => ['I said reported.']]);

    $this->postJson(route('terminal.run'), ['command' => '/sudo'])
        ->assertExactJson([
            'type' => 'window',
            'title' => 'incident-report-003.txt',
            'component' => 'incident-report',
            'props' => ['attempts' => 3],
        ]);
});
