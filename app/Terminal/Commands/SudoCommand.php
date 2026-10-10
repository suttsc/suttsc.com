<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;
use App\Terminal\Output\Window;
use Illuminate\Contracts\Session\Session;

final class SudoCommand extends BaseCommand
{
    private const string SessionKey = 'sudo_attempts';

    public function __construct(
        private readonly Session $session,
    ) {}

    public function signature(): string
    {
        return '/sudo';
    }

    public function description(): string
    {
        return 'Elevate privileges';
    }

    public function handle(): Output
    {
        $attempts = (int) $this->session->get(self::SessionKey, 0) + 1;

        $this->session->put(self::SessionKey, $attempts);

        return match ($attempts) {
            1 => new Inline([
                'suttsc is not in the sudoers file.',
                'This incident will be reported.',
            ]),
            2 => new Inline(['I said reported.']),
            default => new Window(
                sprintf('incident-report-%03d.txt', $attempts),
                'incident-report',
                ['attempts' => $attempts],
            ),
        };
    }
}
