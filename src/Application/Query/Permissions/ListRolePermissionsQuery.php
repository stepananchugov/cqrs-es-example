<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Infrastructure\Share\DBAL\QueryModifier;
use Doctrine\DBAL\Query\QueryBuilder;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

class ListRolePermissionsQuery implements SerializablePayload, QueryModifier
{
    private ?string $roleId = null;

    public function __construct(string $roleId = null)
    {
        $this->roleId = $roleId;
    }

    public function roleId(): ?string
    {
        return $this->roleId;
    }

    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self();
    }

    public function modify(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->select('rp.role_id as role, rp.permission_name as name')
            ->from('role_permissions', 'rp')
        ;

        if (null !== $this->roleId) {
            $queryBuilder->andWhere('rp.role_id = :role_id');
            $queryBuilder->setParameter('role_id', $this->roleId);
        }
    }
}
