<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\Exception;

class ResolverException extends \Exception
{
    public static function unknownMappingType(?string $type): self
    {
        $message = (null === $type) ? 'Unknown mapping type' : sprintf('Unknown mapping type "%s"', $type);

        return new self($message);
    }
}
