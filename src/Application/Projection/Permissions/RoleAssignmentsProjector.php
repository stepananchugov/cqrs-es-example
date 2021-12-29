<?php

declare(strict_types=1);

namespace App\Application\Projection\Permissions;

use App\Domain\Permissions\Event\Realm\RoleAssigned;
use App\Domain\Permissions\Event\Realm\RoleRevoked;
use App\Infrastructure\Share\DBAL\Table;
use EventSauce\EventSourcing\Message;

final class RoleAssignmentsProjector
{
    private Table $roleAssignmentsTable;



    public function __construct(Table $roleAssignmentsTable)
    {
        $this->roleAssignmentsTable = $roleAssignmentsTable;
    }

    public function __invoke(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof RoleAssigned) {
            $this->roleAssignmentsTable->insert(
                [
                    'user_id' => $event->userId()->toString(),
                    'role_id' => $event->roleId(),
                ]
            );
        }

        if ($event instanceof RoleRevoked) {
            $this->roleAssignmentsTable->delete(
                [
                    'user_id' => $event->userId()->toString(),
                    'role_id' => $event->roleId(),
                ]
            );
        }
    }
}
