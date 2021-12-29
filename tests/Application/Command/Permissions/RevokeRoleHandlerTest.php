<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\RevokeRoleHandler;
use EventSauce\EventSourcing\AggregateRootRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class RevokeRoleHandlerTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $adminUserCollectionRepository = $this->createMock(AggregateRootRepository::class);
        $realmRepository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;
        $handler = new RevokeRoleHandler($realmRepository, $adminUserCollectionRepository);

        static::assertInstanceOf(RevokeRoleHandler::class, $handler);
    }

    public function testItHandlesCommand(): void
    {
        static::markTestIncomplete('Lenivo');
    }

    public function testItThrowsForMissingRealm(): void
    {
        static::markTestIncomplete('Lenivo');
    }
}
