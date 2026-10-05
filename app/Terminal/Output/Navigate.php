<?php

declare(strict_types=1);

namespace App\Terminal\Output;

final readonly class Navigate extends Output
{
    /**
     * @param  string  $route  A route name, never a URL.
     */
    public function __construct(
        public string $route,
    ) {}

    public function type(): string
    {
        return 'navigate';
    }

    protected function payload(): array
    {
        return ['url' => route($this->route, absolute: false)];
    }
}
