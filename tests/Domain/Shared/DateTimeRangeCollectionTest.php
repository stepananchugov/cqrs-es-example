<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared;

use App\Domain\OuterFrame\ValueObject\Skin;
use App\Domain\Shared\DateTimeRangeCollection;
use App\Domain\Shared\Exception\UnexpectedValueException;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\Shared\ValueObject\DateTimeRange;
use App\Domain\Translation\Language\LanguageId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Shared\DateTimeRangeCollection
 */
final class DateTimeRangeCollectionTest extends TestCase
{
    /**
     * @var LanguageId[]
     */
    private array $languageIds = [];

    protected function setUp(): void
    {
        $this->languageIds = LanguageId::fromArray(['f8b9ebb4-70fa-4f66-8665-2448402e6591']);
    }

    /**
     * @dataProvider correctProvider
     */
    public function testCheckCorrectRange(array $skins, DateTimeRange $currentRange): void
    {
        static::assertTrue((new DateTimeRangeCollection($skins))->checkCorrectRange($currentRange, $this->languageIds));
    }

    /**
     * @dataProvider incorrectProvider
     */
    public function testCheckIncorrectRange(array $skins, DateTimeRange $currentRange): void
    {
        static::expectException(UnexpectedValueException::class);
        (new DateTimeRangeCollection($skins))->checkCorrectRange($currentRange, $this->languageIds);
    }

    public function correctProvider(): array
    {
        $this->languageIds = LanguageId::fromArray(['f8b9ebb4-70fa-4f66-8665-2448402e6591']);

        return [
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 13:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2010-10-12 11:00:00'), DateTime::fromString('2020-10-12 11:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2030-12-12 11:00:00'), DateTime::fromString('2040-12-12 11:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('3020-10-12 12:00:00'),
                        DateTime::fromString('3030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2030-12-12 11:00:00'), DateTime::fromString('2030-12-12 12:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('3020-10-12 12:00:00'),
                        DateTime::fromString('3030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2010-12-12 11:00:00'), DateTime::fromString('2010-12-12 12:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('3020-10-12 12:00:00'),
                        DateTime::fromString('3030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('3040-12-12 11:00:00'), DateTime::fromString('3040-12-12 12:00:00')),
            ],
        ];
    }

    public function incorrectProvider(): array
    {
        $this->languageIds = LanguageId::fromArray(['f8b9ebb4-70fa-4f66-8665-2448402e6591']);

        return [
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:01:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2010-12-12 11:00:00'), DateTime::fromString('2030-12-12 12:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('3020-10-12 12:00:00'),
                        DateTime::fromString('3030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2010-12-12 11:00:00'), DateTime::fromString('2030-12-12 12:00:00')),
            ],
            [
                [
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('2020-10-12 12:00:00'),
                        DateTime::fromString('2030-10-12 12:00:00'),
                    )),
                    new Skin('aa', $this->languageIds, new DateTimeRange(
                        DateTime::fromString('3020-10-12 12:00:00'),
                        DateTime::fromString('3030-10-12 12:00:00'),
                    )),
                ],
                new DateTimeRange(DateTime::fromString('2010-12-12 11:00:00'), DateTime::fromString('4010-12-12 12:00:00')),
            ],
        ];
    }
}
