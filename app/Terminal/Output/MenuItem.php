<?php

declare(strict_types=1);

namespace App\Terminal\Output;

final readonly class MenuItem
{
    /**
     * @param  string  $run  The command to execute when the item is picked.
     */
    public function __construct(
        public string $label,
        public string $hint,
        public string $run,
    ) {}
}
