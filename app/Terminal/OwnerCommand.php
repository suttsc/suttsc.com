<?php

declare(strict_types=1);

namespace App\Terminal;

use Illuminate\Support\Facades\Auth;

/**
 * A command that only exists for the signed-in owner. Everyone else gets a 404
 * and never sees it in the manifest.
 */
abstract class OwnerCommand extends BaseCommand
{
    public function authorized(): bool
    {
        return Auth::check();
    }
}
