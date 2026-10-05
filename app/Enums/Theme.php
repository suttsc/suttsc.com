<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Contracts\Session\Session;

enum Theme: string
{
    case Amber = 'amber';
    case Green = 'green';
    case Blue = 'blue';
    case Ember = 'ember';

    public const string SessionKey = 'theme';

    public static function current(Session $session): self
    {
        $stored = $session->get(self::SessionKey);

        return (is_string($stored) ? self::tryFrom($stored) : null) ?? self::Amber;
    }

    public function label(): string
    {
        return match ($this) {
            self::Amber => 'Amber phosphor',
            self::Green => 'Green phosphor',
            self::Blue => 'Cold blue',
            self::Ember => 'Ember',
        };
    }
}
