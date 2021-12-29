<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Infrastructure\Share\DBAL\Table;

class ListRolePermissionsHandler
{
    private Table $rolePermissionsTable;

    public function __construct(Table $rolePermissionsTable)
    {
        $this->rolePermissionsTable = $rolePermissionsTable;
    }

    public function __invoke(ListRolePermissionsQuery $query): array
    {
        return $this->rolePermissionsTable->match($query)->fetchAll();
    }
}
