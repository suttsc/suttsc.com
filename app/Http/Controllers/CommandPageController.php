<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Terminal\CommandRegistry;
use Inertia\Inertia;
use Inertia\Response;

final class CommandPageController extends Controller
{
    /**
     * Show a command's output as a page, for anyone arriving without the palette.
     */
    public function __invoke(string $signature, CommandRegistry $registry): Response
    {
        return Inertia::render('command', [
            'signature' => "/{$signature}",
            'output' => $registry->run($signature)->jsonSerialize(),
        ]);
    }
}
