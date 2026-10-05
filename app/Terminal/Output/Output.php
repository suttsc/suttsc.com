<?php

declare(strict_types=1);

namespace App\Terminal\Output;

use JsonSerializable;

abstract readonly class Output implements JsonSerializable
{
    /**
     * The discriminator the client switches on.
     */
    abstract public function type(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function payload(): array;

    /**
     * @return array<string, mixed>
     */
    final public function jsonSerialize(): array
    {
        return ['type' => $this->type(), ...$this->payload()];
    }
}
