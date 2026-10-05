<?php

declare(strict_types=1);

namespace App\Terminal\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CommandNotFound extends NotFoundHttpException
{
    public static function for(string $input): self
    {
        return new self("No command matches [{$input}].");
    }
}
