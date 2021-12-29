<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\GrantRoleCommand;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\Permissions\RealmId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Command\Permissions\GrantRoleCommand
 */
class GrantRoleCommandTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $command = new GrantRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', RealmId::create());

        static::assertInstanceOf(GrantRoleCommand::class, $command);
    }

    public function testItReturnsUserId(): void
    {
        $userId = AdminUserId::create();
        $command = new GrantRoleCommand($userId, 'ROLE_ADMIN', RealmId::create());

        static::assertEquals($userId, $command->userId());
    }

    public function testItReturnsRoleName(): void
    {
        $command = new GrantRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', RealmId::create());

        static::assertEquals('ROLE_ADMIN', $command->roleId());
    }

    public function testItIsSerializable(): void
    {
        $userId = AdminUserId::create();
        $realmId = RealmId::create();
        $command = new GrantRoleCommand($userId, 'ROLE_ADMIN', $realmId);

        static::assertEquals(
            $command,
            GrantRoleCommand::fromPayload([
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
                'realm' => $realmId->toString(),
            ])
        );
    }

    public function testItReturnsRealmId(): void
    {
        $realmId = RealmId::create();
        $command = new GrantRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', $realmId);

        static::assertEquals($realmId, $command->realmId());
    }

    public function testItIsDeserializable(): void
    {
        $userId = AdminUserId::create();
        $realmId = RealmId::create();

        static::assertEquals(
            GrantRoleCommand::fromPayload([
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
                'realm' => $realmId->toString(),
            ]),
            new GrantRoleCommand($userId, 'ROLE_ADMIN', $realmId)
        );
    }
}
