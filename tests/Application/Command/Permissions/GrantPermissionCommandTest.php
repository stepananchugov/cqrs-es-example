<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\GrantPermissionCommand;
use App\Domain\Permissions\PolicyId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Command\Permissions\GrantPermissionCommand
 */
class GrantPermissionCommandTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');

        static::assertInstanceOf(GrantPermissionCommand::class, $command);
    }

    public function testItReturnsPermissionId(): void
    {
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals('permission', $command->permissionId());
    }

    public function testItReturnsRoleId(): void
    {
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals('ROLE_ADMIN', $command->roleId());
    }

    public function testItReturnsPolicyId(): void
    {
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');
        $command = $command->withPolicyId(PolicyId::defaultPolicyId());

        static::assertEquals(
            PolicyId::defaultPolicyId(),
            $command->policyId()
        );
    }

    public function testItIsSerializable(): void
    {
        $policyId = PolicyId::defaultPolicyId();
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');

        static::assertEquals(
            [
                'permissionId' => 'permission',
                'roleId' => 'ROLE_ADMIN',
                'policyId' => $policyId->toString(),
            ],
            $command->toPayload()
        );
    }

    public function testItIsDeserializable(): void
    {
        $command = new GrantPermissionCommand('permission', 'ROLE_ADMIN');
        $policyId = PolicyId::defaultPolicyId();
        $command = $command->withPolicyId($policyId);

        static::assertEquals(
            $command,
            GrantPermissionCommand::fromPayload([
                'permissionId' => 'permission',
                'roleId' => 'ROLE_ADMIN',
                'policyId' => $policyId->toString(),
            ])
        );
    }
}
