<?php

declare(strict_types=1);

namespace App\Terminal\Output;

final readonly class Window extends Output
{
    /**
     * @param  string  $component  The React component under resources/js/Windows.
     * @param  array<string, mixed>  $props
     */
    public function __construct(
        public string $title,
        public string $component,
        public array $props = [],
    ) {}

    public function type(): string
    {
        return 'window';
    }

    protected function payload(): array
    {
        return [
            'title' => $this->title,
            'component' => $this->component,
            'props' => (object) $this->props,
        ];
    }
}
