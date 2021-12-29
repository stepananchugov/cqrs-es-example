<?php

declare(strict_types=1);

namespace App\Application\Projection;

final class ProjectionException extends \RuntimeException
{
    public static function missingAggregateRootId(): self
    {
        return new self('A message must have an aggregate root ID');
    }
}
