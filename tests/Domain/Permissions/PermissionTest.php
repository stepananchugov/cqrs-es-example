<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions;

use App\Application\Query\Catalog\Event\ListEventsQuery;
use App\Application\Query\Permissions\ListPermissionsQuery;
use App\Domain\Permissions\Exception\InvalidArgumentException;
use App\Domain\Permissions\Permission;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;

/**
 * @coversDefaultClass \App\Domain\Permissions\Permission
 */
class PermissionTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $permission = new Permission(ListEventsQuery::class);

        static::assertInstanceOf(Permission::class, $permission);
    }

    public function testItThrowsForMissingClasses(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Permission::fromClassname('Class\Does\Not\Exist');
    }

    public function testItConvertsToDotNotation(): void
    {
        static::assertEquals(
            'catalog.race_event.create_race_event',
            Permission::classnameToPermission('\App\Application\Command\Catalog\RaceEvent\CreateRaceEventHandler')
        );
    }

    public function testItInitializesFromClassname(): void
    {
        $permission = Permission::fromClassname('\App\Application\Command\Catalog\RaceEvent\CreateRaceEventHandler');

        static::assertEquals(
            'catalog.race_event.create_race_event',
            $permission->name(),
        );
    }

    public function testItTransformsEnvelopes(): void
    {
        $envelope = new Envelope(new ListPermissionsQuery());

        static::assertEquals(
            'permissions.list_permissions',
            Permission::envelopeToPermission($envelope)
        );
    }

    public function testItThrowsForBadStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Classname is empty');

        Permission::classnameToPermission('');
    }

    public function testItIsSerializable(): void
    {
        $permission = new Permission('permission.name');

        static::assertEquals(
            ['name' => 'permission.name'],
            $permission->toPayload(),
        );
    }

    public function testItIsDeserializable(): void
    {
        static::assertEquals(
            new Permission('permission.name'),
            Permission::fromPayload(['name' => 'permission.name'])
        );
    }
}
