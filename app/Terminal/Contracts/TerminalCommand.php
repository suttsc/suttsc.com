<?php

declare(strict_types=1);

namespace App\Terminal\Contracts;

use App\Terminal\Output\Output;

interface TerminalCommand
{
    /**
     * The text that summons the command, with its leading slash.
     */
    public function signature(): string;

    /**
     * The line shown beside the signature in the palette.
     */
    public function description(): string;

    /**
     * Whether the command runs but is left out of the manifest.
     */
    public function hidden(): bool;

    /**
     * Whether the current visitor may know the command exists at all.
     */
    public function authorized(): bool;

    public function handle(): Output;
}
