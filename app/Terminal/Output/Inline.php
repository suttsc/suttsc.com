<?php

declare(strict_types=1);

namespace App\Terminal\Output;

final readonly class Inline extends Output
{
    /**
     * @param  list<string>  $lines
     */
    public function __construct(
        public array $lines,
    ) {}

    public function type(): string
    {
        return 'inline';
    }

    protected function payload(): array
    {
        return ['lines' => $this->lines];
    }
}
