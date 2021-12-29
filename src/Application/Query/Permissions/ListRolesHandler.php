<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Domain\Permissions\Role;
use App\Infrastructure\Permissions\Configuration;
use App\Infrastructure\Share\DBAL\Table;

class ListRolesHandler
{
    private Configuration $configuration;

    private Table $roleAssignmentsTable;

    public function __construct(Configuration $configuration, Table $roleAssignmentsTable)
    {
        $this->configuration = $configuration;
        $this->roleAssignmentsTable = $roleAssignmentsTable;
    }

    public function __invoke(ListRolesQuery $query): array
    {
        $userId = $query->userId();

        if (null === $userId) {
            $result = array_map(static function (Role $role): array {
                return [
                    'id' => $role->id(),
                    'name' => $role->name(),
                ];
            }, $this->configuration->roles());

            $roleId = $query->roleId();

            if (null === $roleId) {
                return $result;
            }

            return array_filter($result, static function ($element) use ($roleId): bool {
                return $element['id'] === $roleId;
            });
        }

        $roleNames = $this->configuration->roleNames();

        return array_map(static function (array $roleRow) use ($roleNames): array {
            $roleId = $roleRow['roleid'];

            return [
                'id' => $roleId,
                'name' => $roleNames[$roleId],
            ];
        }, $this->roleAssignmentsTable->match($query)->fetchAll());
    }
}
