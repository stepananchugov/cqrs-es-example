<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\GrantPermissionCommand;
use App\Application\Command\Permissions\GrantPermissionHandler;
use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Policy;
use EventSauce\EventSourcing\AggregateRootRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class GrantPermissionHandlerTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;

        $handler = new GrantPermissionHandler($repository);

        static::assertInstanceOf(GrantPermissionHandler::class, $handler);
    }

    public function testItHandlesGrantPermissionCommand(): void
    {
        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;

        $repository
            ->expects(static::once())
            ->method('persist')
            ->with(static::callback(static function (Policy $aggregate): bool {
                return $aggregate->isGranted(
                    new Permission('permission_id'),
                    'ROLE_ADMIN',
                );
            }))
        ;

        $handler = new GrantPermissionHandler($repository);

        $handler(new GrantPermissionCommand('permission_id', 'ROLE_ADMIN'));
    }
}
