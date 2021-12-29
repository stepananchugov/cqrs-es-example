<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Domain\Permissions\Permission;
use App\Infrastructure\Permissions\Configuration;
use App\Infrastructure\Share\DBAL\Table;

class ListPermissionsHandler
{
    private Configuration $configuration;

    private Table $rolePermissionsTable;

    public function __construct(Configuration $configuration, Table $rolePermissionsTable)
    {
        $this->configuration = $configuration;
        $this->rolePermissionsTable = $rolePermissionsTable;
    }

    public function __invoke(ListPermissionsQuery $query): array
    {
        // A bit hacky. What this does: it filters if there's a user passed alongside
        // We should have done another separate query instead, e.g. `ListUserPermissionsHandler`
        if ($query->shouldMatch()) {
            return $this->rolePermissionsTable->match($query)->fetchAll();
        }

        return array_map(static function (Permission $permission): array {
            return [
                'name' => $permission->name(),
            ];
        }, $this->configuration->permissions());
    }
}
