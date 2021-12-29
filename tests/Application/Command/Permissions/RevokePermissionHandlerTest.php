<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\Permissions;

use App\Application\Command\Permissions\RevokePermissionCommand;
use App\Application\Command\Permissions\RevokePermissionHandler;
use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Policy;
use App\Domain\Permissions\PolicyId;
use EventSauce\EventSourcing\AggregateRootRepository;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class RevokePermissionHandlerTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;

        $handler = new RevokePermissionHandler($repository);

        static::assertInstanceOf(RevokePermissionHandler::class, $handler);
    }

    public function testItHandlesRevokePermissionCommand(): void
    {
        $repository = $this
            ->getMockBuilder(AggregateRootRepository::class)
            ->getMock()
        ;
        $permission = new Permission('permission_id');
        $policy = Policy::create(PolicyId::defaultPolicyId());
        $policy->grant($permission, 'ROLE_ADMIN');
        $repository
            ->method('retrieve')
            ->with(PolicyId::defaultPolicyId())
            ->willReturn($policy)
        ;
        $repository
            ->expects(static::once())
            ->method('persist')
            ->with(static::callback(static function (Policy $aggregate): bool {
                return !$aggregate->isGranted(
                    new Permission('permission_id'),
                    'ROLE_ADMIN',
                );
            }))
        ;

        $handler = new RevokePermissionHandler($repository);

        $handler(new RevokePermissionCommand('permission_id', 'ROLE_ADMIN'));
    }
}
