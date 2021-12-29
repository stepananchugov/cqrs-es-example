<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Catalog\RaceEvent\Event\RaceEventCreated;
use App\Domain\ForeignData\Event\SyncSucceeded;
use App\Infrastructure\AmqpClient\RabbitStatusCommand;

class EventSubscriptionMap
{
    private static array $map = [
        'raceEvents' => RaceEventCreated::class,
        'checkRabbit' => RabbitStatusCommand::class,
        'synchronization' => SyncSucceeded::class,
    ];

    public static function map(): array
    {
        return self::$map;
    }

    public static function eventAffectsSubscription(string $eventName, string $subscriptionName): bool
    {
        return \array_key_exists($subscriptionName, self::$map) && self::$map[$subscriptionName] === $eventName;
    }
}
