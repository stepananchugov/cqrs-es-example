<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Validator;

final class ValidatorException extends \InvalidArgumentException
{
    public static function badCheckType(string $type, array $allowedTypes): self
    {
        return new self(sprintf(
            'Bad check type passed: %s. Should be one of: %s',
            $type,
            implode(', ', $allowedTypes)
        ));
    }

    /**
     * @param mixed $value
     */
    public static function notAnAggregateRootId($value): self
    {
        return new self(sprintf(
            'Expected an aggregate root ID, got %s instead',
            \is_object($value) ? \get_class($value) : \gettype($value)
        ));
    }
}
