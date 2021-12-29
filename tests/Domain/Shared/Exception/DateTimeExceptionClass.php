<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\Exception;

use App\Domain\Shared\Exception\DateTimeException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass
 */
class DateTimeExceptionClass extends TestCase
{
    public function testItReturnsBadFormatException(): void
    {
        $exception = DateTimeException::badFormat('format', 'dateTime');

        static::assertEquals(
            'Cannot create a date for \'dateTime\' with format \'format\'',
            $exception->getMessage()
        );
    }
}
