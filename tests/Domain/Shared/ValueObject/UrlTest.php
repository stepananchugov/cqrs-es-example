<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\UrlException;
use App\Domain\Shared\ValueObject\Url;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Shared\ValueObject\Url
 */
class UrlTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $url = new Url('http://google.com');

        static::assertInstanceOf(Url::class, $url);
    }

    public function testItThrowsForInvalidUrls(): void
    {
        $this->expectException(UrlException::class);
        $this->expectExceptionMessage('`test` is not a valid URL');

        $url = new Url('test');
    }

    public function testItIsComparable(): void
    {
        $url1 = new Url('http://google.com');
        $url2 = new Url('http://yandex.ru');
        $url3 = new Url('http://google.com');

        static::assertTrue($url1->equalsTo($url3));
        static::assertFalse($url1->equalsTo($url2));
    }
}
