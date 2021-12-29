<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\GrantRoleCommand;
use App\Application\Command\Permissions\GrantRoleHandler;
use App\Domain\AdminUser\AdminUser;
use App\Domain\AdminUser\AdminUserCollection;
use App\Domain\AdminUser\AdminUserCollectionId;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\Permissions\Realm;
use App\Domain\Permissions\RealmId;
use EventSauce\EventSourcing\AggregateRootRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class GrantRoleHandlerTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;
        $handler = new GrantRoleHandler($repository, $repository);

        static::assertInstanceOf(GrantRoleHandler::class, $handler);
    }

    public function testItHandlesGrantRoleCommand(): void
    {
        $adminUserId = AdminUserId::create();
        $realmId = RealmId::create();

        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;
        $repository
            ->expects(static::once())
            ->method('retrieve')
            ->with($realmId)
        ;

        $repository
            ->expects(static::once())
            ->method('persist')
            ->with(static::callback(static function (Realm $realm) use ($adminUserId): bool {
                return $realm->userHasRole($adminUserId, 'ROLE_ADMIN');
            }))
        ;
        $userCollection = $this->createMock(AdminUserCollection::class);
        $userCollection
            ->method('getUserById')
            ->willReturn(new AdminUser($adminUserId, 'username'))
        ;
        $userCollectionRepository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;
        $userCollectionRepository
            ->method('retrieve')
            ->with(AdminUserCollectionId::motorsportTicketsId())
            ->willReturn($userCollection)
        ;

        $handler = new GrantRoleHandler($repository, $userCollectionRepository);
        $handler(new GrantRoleCommand(
            $adminUserId,
            'ROLE_ADMIN',
            $realmId
        ));
    }
}
