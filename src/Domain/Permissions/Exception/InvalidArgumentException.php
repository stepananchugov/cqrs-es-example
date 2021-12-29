<?php

declare(strict_types=1);

namespace App\Domain\Permissions\Exception;

final class InvalidArgumentException extends \InvalidArgumentException
{
    public static function missingPermissionClass(string $className): self
    {
        return new self(sprintf(
            'Class `%s` does not exist',
            $className
        ));
    }

    public static function invalidClassname(string $className): self
    {
        if ('' === $className) {
            return new self('Classname is empty');
        }

        return new self(sprintf(
            'Classname `%s` is invalid',
            $className
        ));
    }
}
