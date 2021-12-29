<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions\Event\Realm;

use App\Domain\AdminUser\AdminUserId;
use App\Domain\Permissions\Event\Realm\RoleAssigned;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Permissions\Event\Realm\RoleAssigned
 */
class RoleAssignedTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $event = new RoleAssigned(AdminUserId::create(), 'ROLE_ADMIN');

        static::assertInstanceOf(RoleAssigned::class, $event);
    }

    public function testItReturnsUserId(): void
    {
        $userId = AdminUserId::create();
        $event = new RoleAssigned($userId, 'ROLE_ADMIN');

        static::assertEquals($userId, $event->userId());
    }

    public function testItReturnsRole(): void
    {
        $event = new RoleAssigned(AdminUserId::create(), 'ROLE_ADMIN');

        static::assertEquals('ROLE_ADMIN', $event->roleId());
    }

    public function testItIsSerializable(): void
    {
        $userId = AdminUserId::create();
        $event = new RoleAssigned($userId, 'ROLE_ADMIN');

        static::assertEquals(
            [
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
            ],
            $event->toPayload()
        );
    }

    public function testItIsDeserializable(): void
    {
        $userId = AdminUserId::create();

        static::assertEquals(
            new RoleAssigned($userId, 'ROLE_ADMIN'),
            RoleAssigned::fromPayload([
                'userId' => $userId->toString(),
                'roleId' => 'ROLE_ADMIN',
            ])
        );
    }
}
