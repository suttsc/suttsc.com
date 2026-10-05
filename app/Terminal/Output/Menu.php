<?php

declare(strict_types=1);

namespace App\Terminal\Output;

final readonly class Menu extends Output
{
    /**
     * @param  list<MenuItem>  $items
     */
    public function __construct(
        public string $label,
        public array $items,
    ) {}

    public function type(): string
    {
        return 'menu';
    }

    protected function payload(): array
    {
        return ['label' => $this->label, 'items' => $this->items];
    }
}
