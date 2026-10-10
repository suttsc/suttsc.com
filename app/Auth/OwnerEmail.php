<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Support\Str;

final class OwnerEmail
{
    /**
     * Whether the address is the owner's. Exact match only — never a domain
     * check, never a substring.
     */
    public static function matches(?string $email): bool
    {
        $owner = config('auth.owner_email');

        if (! is_string($owner) || $owner === '' || $email === null) {
            return false;
        }

        return hash_equals(self::normalise($owner), self::normalise($email));
    }

    private static function normalise(string $email): string
    {
        return Str::lower(trim($email));
    }
}
