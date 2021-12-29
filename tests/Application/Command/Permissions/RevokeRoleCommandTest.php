<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\RevokeRoleCommand;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\Permissions\RealmId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Command\Permissions\RevokeRoleCommand
 */
class RevokeRoleCommandTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $command = new RevokeRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', RealmId::create());

        static::assertInstanceOf(RevokeRoleCommand::class, $command);
    }

    public function testItReturnsUserId(): void
    {
        $userId = AdminUserId::create();
        $command = new RevokeRoleCommand($userId, 'ROLE_ADMIN', RealmId::create());

        static::assertEquals($userId, $command->userId());
    }

    public function testItReturnsRoleName(): void
    {
        $command = new RevokeRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', RealmId::create());

        static::assertEquals('ROLE_ADMIN', $command->roleId());
    }

    public function testItReturnsRealmId(): void
    {
        $realmId = RealmId::create();
        $command = new RevokeRoleCommand(AdminUserId::create(), 'ROLE_ADMIN', $realmId);

        static::assertEquals($realmId, $command->realmId());
    }

    public function testItIsDeserializable(): void
    {
        $userId = AdminUserId::create();
        $realmId = RealmId::create();
        $command = new RevokeRoleCommand($userId, 'ROLE_ADMIN', $realmId);

        static::assertEquals(
            $command,
            RevokeRoleCommand::fromPayload([
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
                'realmId' => $realmId->toString(),
            ])
        );
    }

    public function testItIsSerializable(): void
    {
        $userId = AdminUserId::create();
        $realmId = RealmId::create();
        $command = new RevokeRoleCommand($userId, 'ROLE_ADMIN', $realmId);

        static::assertEquals(
            [
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
                'realmId' => $realmId->toString(),
            ],
            $command->toPayload(),
        );
    }
}
