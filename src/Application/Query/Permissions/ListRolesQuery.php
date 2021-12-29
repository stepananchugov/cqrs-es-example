<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Domain\Shared\ObjectId;
use App\Infrastructure\Share\DBAL\QueryModifier;
use Doctrine\DBAL\Query\QueryBuilder;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

class ListRolesQuery implements SerializablePayload, QueryModifier
{
    private ?ObjectId $userId = null;

    private ?string $roleId = null;

    private ?string $username = null;

    public function modify(QueryBuilder $queryBuilder): void
    {
        if (null !== $this->roleId || null !== $this->userId || null !== $this->username) {
            $queryBuilder->select('ra.role_id as roleId');
            $queryBuilder->from('role_assignments', 'ra');
        }

        if (null !== $this->roleId) {
            $queryBuilder->andWhere('ra.role_id = :role_id');
            $queryBuilder->setParameter('role_id', $this->roleId);
        }

        if (null !== $this->userId) {
            $queryBuilder->andWhere('ra.user_id = :user_id');
            $queryBuilder->setParameter('user_id', $this->userId->toString());
        }

        if (null !== $this->username) {
            $queryBuilder->leftJoin('ra', 'admin_user', 'u', 'u.id = ra.user_id');
            $queryBuilder->andWhere('u.username = :username');
            $queryBuilder->setParameter('username', $this->username);
        }
    }

    public function userId(): ?ObjectId
    {
        return $this->userId;
    }

    public function roleId(): ?string
    {
        return $this->roleId;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function withUserId(ObjectId $userId): self
    {
        $clone = clone $this;
        $clone->userId = $userId;

        return $clone;
    }

    public function withRoleId(string $roleId): self
    {
        $clone = clone $this;
        $clone->roleId = $roleId;

        return $clone;
    }

    public function withUsername(string $username): self
    {
        $clone = clone $this;
        $clone->username = $username;

        return $clone;
    }

    public function toPayload(): array
    {
        $result = [];

        if (null !== $this->userId) {
            $result['userId'] = $this->userId->toString();
        }

        if (null !== $this->roleId) {
            $result['roleId'] = $this->roleId;
        }

        return $result;
    }

    public static function fromPayload(array $payload): self
    {
        $instance = new self();

        if (\array_key_exists('userId', $payload)) {
            $instance = $instance->withUserId(ObjectId::fromString($payload['userId']));
        }

        if (\array_key_exists('roleId', $payload)) {
            $instance = $instance->withRoleId($payload['roleId']);
        }

        return $instance;
    }
}
