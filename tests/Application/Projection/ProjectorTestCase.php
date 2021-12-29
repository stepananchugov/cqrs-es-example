<?php

declare(strict_types=1);

namespace App\Tests\Application\Projection;

use App\Application\Projection\Catalog\RaceEventsProjector;
use App\Domain\Catalog\RaceEvent\Event\RaceEventCreated;
use App\Tests\Application\Projection\ProjectorTest\Expectation;
use App\Tests\Helper\StubId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;

abstract class ProjectorTestCase extends TestCase
{
    public function provideProjectorExpectations(): array
    {
        return [
            'EventCreated on EventsProjector' => [
                RaceEventsProjector::class,
                RaceEventCreated::class,
            ],
        ];
    }

    public static function assertProjectorCalls(string $projectorClass, string $eventClass, Expectation $expectation, string $aggregateRootId = null): void
    {
        static::assertTrue(class_exists($projectorClass), sprintf(
            'Projector class %s does not exist',
            $projectorClass,
        ));

        $table = $expectation->createMock();
        $projector = new $projectorClass($table);
        $payload = $expectation->payload();
        $event = $eventClass::fromPayload($payload);

        if (null !== $aggregateRootId) {
            $stubId = StubId::fromString($aggregateRootId);
        } else {
            $stubId = StubId::create();
        }

        $message = new Message($event, [Header::AGGREGATE_ROOT_ID => $stubId]);

        static::assertIsCallable($projector);
        $projector($message);
    }
}
