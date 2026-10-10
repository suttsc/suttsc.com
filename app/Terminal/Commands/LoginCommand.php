<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Navigate;
use App\Terminal\Output\Output;
use Illuminate\Support\Facades\Auth;

final class LoginCommand extends BaseCommand
{
    public function signature(): string
    {
        return '/login';
    }

    public function description(): string
    {
        return 'Prove it';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function handle(): Output
    {
        if (Auth::check()) {
            return new Inline(['Already signed in. Still suttsc.']);
        }

        return new Navigate('auth.google.redirect');
    }
}
