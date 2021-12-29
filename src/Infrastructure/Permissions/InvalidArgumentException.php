<?php

declare(strict_types=1);

namespace App\Infrastructure\Permissions;

use JetBrains\PhpStorm\Pure;

final class InvalidArgumentException extends \InvalidArgumentException
{
    #[Pure]
    public static function invalidConfiguration(string $reason = ''): self
    {
        return new self(implode(' ', ['Permission configuration is invalid!', $reason]));
    }
}
