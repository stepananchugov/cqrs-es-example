<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Shared\ValueObject\DateTime
 */
class DateTimeTest extends TestCase
{
    public function testItIsEquatable(): void
    {
        $dt1 = DateTime::fromString('2012-01-01 12:00:00');
        $dt2 = DateTime::fromString('2012-01-01 12:00:00');

        static::assertTrue($dt1->equalsTo($dt2));
    }

    public function testItIsComparable(): void
    {
        $dt1 = DateTime::fromString('2012-01-01 11:00:00');
        $dt2 = DateTime::fromString('2012-01-01 12:00:00');

        static::assertTrue($dt2->isGreaterThan($dt1));
        static::assertTrue($dt1->isLessThan($dt2));
    }

    public function testItCanBeCreatedForNow(): void
    {
        $dt = DateTime::now();

        static::assertInstanceOf(DateTime::class, $dt);
    }

    public function testItCanBeCreatedFromSerializedState(): void
    {
        $dt = DateTime::fromPayload(['dateTime' => '2010-01-01 12:00:00']);

        static::assertInstanceOf(DateTime::class, $dt);
        static::assertTrue($dt->equalsTo(DateTime::fromString('2010-01-01 12:00:00')));
    }

    public function testItCanBeCreatedFromNativeDatetime(): void
    {
        $dt = DateTime::fromNative(new \DateTimeImmutable());

        static::assertInstanceOf(DateTime::class, $dt);
    }

    public function testItSerializes(): void
    {
        $serialized = ['dateTime' => '2010-01-01 12:00:00'];
        $dt = DateTime::fromPayload($serialized);

        static::assertEquals($serialized, $dt->toPayload());
    }

    public function testItThrowsForBadFormat(): void
    {
        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessage('Cannot create a date for \'2012-01 11:00:00\' with format \'Y-m-d H:i:s\'');

        $dt = DateTime::fromString('2012-01 11:00:00');
    }

    public function testItConvertsToNative(): void
    {
        $dt = DateTime::fromString('2012-01-01 11:00:00');

        $native = $dt->toNative();
        static::assertInstanceOf(\DateTimeImmutable::class, $native);
        static::assertEquals('2012-01-01 11:00:00', $native->format('Y-m-d H:i:s'));
    }

    public function testItConvertsToString(): void
    {
        $dateTime = '2012-01-01 11:00:00';
        $dt = DateTime::fromString($dateTime);

        static::assertEquals('2012-01-01 11:00:00', $dt->toString());
    }

    public function testItThrowsForInvalidStringRepresentations(): void
    {
        $this->expectException(DateTimeException::class);

        $dt = DateTime::fromString('22-01-01 120000');
    }
}
