<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Domain\User\UserId;
use App\Infrastructure\Share\DBAL\QueryModifier;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class ListPermissionsQuery implements SerializablePayload, QueryModifier
{
    private ?array $roleIds = [];

    private ?UserId $userId = null;

    private ?string $username = null;

    public function withRoleIds(array $roleIds): self
    {
        $clone = clone $this;
        $clone->roleIds = $roleIds;

        return $clone;
    }

    public function withUserId(UserId $userId): self
    {
        $clone = clone $this;
        $clone->userId = $userId;

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

        if (null !== $this->roleIds) {
            $result['roleIds'] = $this->roleIds;
        }

        if (null !== $this->userId) {
            $result['userId'] = $this->userId->toString();
        }

        if (null !== $this->username) {
            $result['username'] = $this->username;
        }

        return $result;
    }

    public static function fromPayload(array $payload): self
    {
        $instance = new self();

        if (\array_key_exists('roleIds', $payload)) {
            $instance->roleIds = $payload['roleIds'];
        }

        if (\array_key_exists('userId', $payload)) {
            $instance->userId = UserId::fromString($payload['userId']);
        }

        if (\array_key_exists('username', $payload)) {
            $instance->username = $payload['username'];
        }

        return $instance;
    }

    public function roleIds(): ?array
    {
        return $this->roleIds;
    }

    public function userId(): ?UserId
    {
        return $this->userId;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function shouldMatch(): bool
    {
        return $this->userId instanceof UserId
            || null !== $this->username
            || (\is_array($this->roleIds) && \count($this->roleIds) > 0);
    }

    public function modify(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select('rp.permission_name as name');
        $queryBuilder->from('role_permissions', 'rp');

        if (null !== $this->userId || null !== $this->username) {
            $queryBuilder->leftJoin('rp', 'role_assignments', 'ra', 'ra.role_id = rp.role_id');
            $queryBuilder->leftJoin('rp', 'admin_user', 'u', 'u.id = ra.user_id');

            if (null !== $this->userId) {
                $queryBuilder->andWhere('u.id = :user_id');
                $queryBuilder->setParameter('user_id', $this->userId->toString());
            }

            if (null !== $this->username) {
                $queryBuilder->andWhere('u.username = :username');
                $queryBuilder->setParameter('username', $this->username);
            }
        }

        if (\is_array($this->roleIds) && \count($this->roleIds) > 0) {
            $queryBuilder->leftJoin('rp', 'role_assignments', 'ra', 'ra.role_id = rp.role_id');
            $queryBuilder->andWhere('rp.role_id IN (:role_ids)');
            $queryBuilder->setParameter('role_ids', $this->roleIds, Connection::PARAM_STR_ARRAY);
        }
    }
}
