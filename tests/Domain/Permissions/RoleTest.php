<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions;

use App\Domain\Permissions\Role;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Permissions\Role
 */
class RoleTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $role = new Role(
            'ROLE_ADMIN',
            'Administrator',
        );

        static::assertInstanceOf(Role::class, $role);
    }

    public function testItReturnsId(): void
    {
        $role = new Role(
            'ROLE_ADMIN',
            'Administrator',
        );

        static::assertEquals('ROLE_ADMIN', $role->id());
    }

    public function testItReturnsName(): void
    {
        $role = new Role(
            'ROLE_ADMIN',
            'Administrator',
        );

        static::assertEquals('Administrator', $role->name());
    }

    public function testItIsSerializable(): void
    {
        static::assertEquals(
            [
                'id' => 'ROLE_ADMIN',
                'name' => 'Administrator',
            ],
            (new Role('ROLE_ADMIN', 'Administrator'))->toPayload(),
        );
    }

    public function testItIsDeserializable(): void
    {
        static::assertEquals(
            Role::fromPayload([
                'id' => 'ROLE_ADMIN',
                'name' => 'Administrator',
            ]),
            new Role('ROLE_ADMIN', 'Administrator'),
        );
    }
}
