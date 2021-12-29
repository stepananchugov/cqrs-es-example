<?php

declare(strict_types=1);

namespace App\Domain\Permissions\Event\Realm;

use App\Domain\User\UserId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class RoleAssigned implements SerializablePayload
{
    private UserId $userId;

    private string $roleId;

    public function __construct(UserId $userId, string $roleId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function roleId(): string
    {
        return $this->roleId;
    }

    public function toPayload(): array
    {
        return [
            'userId' => $this->userId->toString(),
            'roleId' => $this->roleId,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            UserId::fromString($payload['userId']),
            $payload['roleId'],
        );
    }
}
