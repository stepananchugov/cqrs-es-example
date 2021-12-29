<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\User\UserId;
use App\Domain\Permissions\RealmId;
use App\Domain\Shared\ObjectId;
use App\Infrastructure\Permissions\Validator as Permissions;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Validator\Constraints as Assert;

class GrantRoleCommand implements SerializablePayload
{
    // Motivation is, permission domain has nothing to do with user domain, directly
    // Not 100% sure about this though
    private ObjectId $userId;

    private RealmId $realmId;

    /**
     * @Assert\NotBlank()
     * @Permissions\RoleId()
     */
    private string $roleId;

    public function __construct(ObjectId $userId, string $roleId, RealmId $realmId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->realmId = $realmId;
    }

    public function userId(): ObjectId
    {
        return $this->userId;
    }

    public function roleId(): string
    {
        return $this->roleId;
    }

    public function realmId(): RealmId
    {
        return $this->realmId;
    }

    public function toPayload(): array
    {
        return [
            'userId' => $this->userId->toString(),
            'roleId' => $this->roleId,
            'realm' => $this->realmId,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            UserId::fromString($payload['userId']),
            $payload['roleId'],
            RealmId::fromString($payload['realm']),
        );
    }
}
