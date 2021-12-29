<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class ObjectId implements AggregateRootId
{
    private UuidInterface $uuid;

    final protected function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function toUuid(): UuidInterface
    {
        return Uuid::fromString($this->uuid->toString());
    }

    public function equals(self $objectId): bool
    {
        return $this->uuid->equals($objectId->uuid);
    }

    /**
     * @return static
     */
    final public static function create(): self
    {
        return new static(Uuid::uuid4());
    }

    /**
     * @return static
     */
    final public static function fromUuid(UuidInterface $uuid): self
    {
        return new static($uuid);
    }

    /**
     * @return static
     */
    final public static function fromString(string $aggregateRootId): self
    {
        return static::fromUuid(Uuid::fromString($aggregateRootId));
    }
}
