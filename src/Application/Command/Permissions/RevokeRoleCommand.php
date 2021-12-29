<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\User\UserId;
use App\Domain\Permissions\RealmId;
use App\Infrastructure\Permissions\Validator as Permissions;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Validator\Constraints as Assert;

class RevokeRoleCommand implements SerializablePayload
{
    private UserId $userId;

    /**
     * @Assert\NotBlank()
     * @Permissions\RoleId()
     */
    private string $roleId;

    private RealmId $realmId;

    public function __construct(UserId $userId, string $roleId, RealmId $realmId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->realmId = $realmId;
    }

    public function userId(): UserId
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
            'realmId' => $this->realmId->toString(),
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            UserId::fromString($payload['userId']),
            $payload['roleId'],
            RealmId::fromString($payload['realmId']),
        );
    }
}
