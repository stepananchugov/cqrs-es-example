<?php

declare(strict_types=1);

namespace App\Tests\Application\Projection\Permissions;

use App\Application\Projection\Permissions\RoleAssignmentsProjector;
use App\Infrastructure\Share\DBAL\Table;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Projection\Permissions\RoleAssignmentsProjector
 */
class RoleAssignmentsProjectorTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $table = $this
            ->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $projector = new RoleAssignmentsProjector($table);

        static::assertInstanceOf(RoleAssignmentsProjector::class, $projector);
    }
}
