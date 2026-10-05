<?php

declare(strict_types=1);

namespace App\Terminal;

/**
 * The words typed after a command's signature. Inject it through a command's
 * constructor when the command takes arguments.
 */
final readonly class Input
{
    /**
     * @param  list<string>  $arguments
     */
    public function __construct(
        public array $arguments = [],
    ) {}

    public function argument(int $position): ?string
    {
        return $this->arguments[$position] ?? null;
    }
}
