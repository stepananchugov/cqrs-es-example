<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Infrastructure\Permissions\Configuration;
use App\Infrastructure\Share\DBAL\Table;

final class GetUserRolesHandler
{
    private Table $roleAssignmentsTable;

    private Configuration $configuration;

    public function __construct(Table $roleAssignmentsTable, Configuration $configuration)
    {
        $this->roleAssignmentsTable = $roleAssignmentsTable;
        $this->configuration = $configuration;
    }

    public function __invoke(GetUserRolesQuery $query): array
    {
        // This is a wrapper. Or a retranslator.
        // Might seem pointless, but we actually pin the real usecase down here.
        $listRolesQuery = (new ListRolesQuery())->withUserId($query->userId());
        $roleNames = $this->configuration->roleNames();

        return array_map(static function (array $roleRow) use ($roleNames): array {
            $roleId = $roleRow['roleid'];

            return [
                'id' => $roleId,
                'name' => $roleNames[$roleId],
            ];
        }, $this->roleAssignmentsTable->match($listRolesQuery)->fetchAll());
    }
}
