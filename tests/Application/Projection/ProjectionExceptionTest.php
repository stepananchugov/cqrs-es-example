<?php

declare(strict_types=1);

namespace App\Tests\Application\Projection;

use App\Application\Projection\ProjectionException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Projection\ProjectionException
 */
class ProjectionExceptionTest extends TestCase
{
    public function testItReturnsMissingAggregateRootException(): void
    {
        $exception = ProjectionException::missingAggregateRootId();

        static::assertEquals(
            'A message must have an aggregate root ID',
            $exception->getMessage()
        );
    }
}
