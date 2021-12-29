<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions\Event\Policy;

use App\Domain\Permissions\Event\Policy\PermissionRevoked;
use App\Domain\Permissions\Permission;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @coversDefaultClass \App\Domain\Permissions\Event\Policy\PermissionRevoked
 */
class PermissionRevokedTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $event = new PermissionRevoked(new Permission('create_event'), Uuid::uuid4()->toString());

        static::assertInstanceOf(PermissionRevoked::class, $event);
    }

    public function testItReturnsPermission(): void
    {
        $permission = new Permission('create_event');
        $event = new PermissionRevoked($permission, Uuid::uuid4()->toString());

        static::assertEquals($permission, $event->permission());
    }

    public function testItReturnsRoleId(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $event = new PermissionRevoked(new Permission('create_event'), $uuid);

        static::assertEquals($uuid, $event->roleId());
    }

    public function testItIsSerializable(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $event = new PermissionRevoked(new Permission('create_event'), $uuid);

        static::assertEquals([
            'roleId' => $uuid,
            'permission' => [
                'name' => 'create_event',
            ],
        ], $event->toPayload());
    }

    public function testItIsDeserializable(): void
    {
        $uuid = Uuid::uuid4()->toString();

        static::assertEquals(
            new PermissionRevoked(new Permission('create_event'), $uuid),
            PermissionRevoked::fromPayload([
                'roleId' => $uuid,
                'permission' => [
                    'name' => 'create_event',
                ],
            ])
        );
    }
}
