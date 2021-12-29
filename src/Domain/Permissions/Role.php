<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class Role implements SerializablePayload
{
    private string $id;

    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * @return Role
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['id'],
            $payload['name']
        );
    }
}
