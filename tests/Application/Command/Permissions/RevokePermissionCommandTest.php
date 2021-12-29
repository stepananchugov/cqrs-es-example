<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\RevokePermissionCommand;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Command\Permissions\RevokePermissionCommand
 */
class RevokePermissionCommandTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $command = new RevokePermissionCommand('permission', 'ROLE_ADMIN');

        static::assertInstanceOf(RevokePermissionCommand::class, $command);
    }

    public function testItReturnsPermissionId(): void
    {
        $command = new RevokePermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals('permission', $command->permissionId());
    }

    public function testItReturnsRoleId(): void
    {
        $command = new RevokePermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals('ROLE_ADMIN', $command->roleId());
    }

    public function testItIsSerializable(): void
    {
        $command = new RevokePermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals(
            [
                'permissionId' => 'permission',
                'roleId' => 'ROLE_ADMIN',
            ],
            $command->toPayload()
        );
    }

    public function testItIsDeserializable(): void
    {
        static::assertEquals(
            new RevokePermissionCommand('permission', 'ROLE_ADMIN'),
            RevokePermissionCommand::fromPayload([
                'permissionId' => 'permission',
                'roleId' => 'ROLE_ADMIN',
            ])
        );
    }
}
