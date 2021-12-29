<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions;

use App\Domain\Permissions\Event\Policy\PermissionGranted;
use App\Domain\Permissions\Event\Policy\PermissionRevoked;
use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Policy;
use App\Domain\Permissions\PolicyId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Permissions\Policy
 */
class PolicyTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $rolePermission = Policy::create(PolicyId::defaultPolicyId());

        static::assertInstanceOf(Policy::class, $rolePermission);
    }

    public function testItAppliesPermissionGranted(): void
    {
        $permission = new Permission('permission_name');
        $rolePermission = Policy::create(PolicyId::defaultPolicyId());
        $rolePermission->applyPermissionGranted(
            new PermissionGranted(
                $permission,
                'ROLE_ADMIN'
            )
        );

        static::assertTrue($rolePermission->isGranted(
            $permission,
            'ROLE_ADMIN'
        ));
    }

    public function testItAppliesPermissionRevoked(): void
    {
        $permission = new Permission('permission_name');
        $rolePermission = Policy::create(PolicyId::defaultPolicyId());
        $rolePermission->applyPermissionGranted(
            new PermissionGranted(
                $permission,
                'ROLE_ADMIN'
            )
        );

        $rolePermission->applyPermissionRevoked(
            new PermissionRevoked(
                $permission,
                'ROLE_ADMIN'
            )
        );

        static::assertFalse($rolePermission->isGranted(
            $permission,
            'ROLE_ADMIN'
        ));
    }
}
