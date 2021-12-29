<?php

declare(strict_types=1);

namespace App\Application\Projection\Permissions;

use App\Domain\Permissions\Event\Policy\PermissionGranted;
use App\Domain\Permissions\Event\Policy\PermissionRevoked;
use App\Infrastructure\Share\DBAL\Table;
use EventSauce\EventSourcing\Message;

final class RolePermissionsProjector
{
    private Table $rolePermissionsTable;

    public function __construct(Table $rolePermissionsTable)
    {
        $this->rolePermissionsTable = $rolePermissionsTable;
    }

    public function __invoke(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof PermissionGranted) {
            $this->rolePermissionsTable->insert([
                'role_id' => $event->roleId(),
                'permission_name' => $event->permission()->name(),
            ]);
        }

        if ($event instanceof PermissionRevoked) {
            $this->rolePermissionsTable->delete([
                'role_id' => $event->roleId(),
                'permission_name' => $event->permission()->name(),
            ]);
        }
    }
}
