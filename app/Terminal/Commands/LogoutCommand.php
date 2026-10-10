<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;
use App\Terminal\OwnerCommand;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

final class LogoutCommand extends OwnerCommand
{
    public function __construct(
        private readonly Session $session,
    ) {}

    public function signature(): string
    {
        return '/logout';
    }

    public function description(): string
    {
        return 'Go back to being everyone';
    }

    public function handle(): Output
    {
        Auth::guard('web')->logout();

        $this->session->invalidate();
        $this->session->regenerateToken();

        return new Inline(['Signed out. You are suttsc again, unofficially.']);
    }
}
