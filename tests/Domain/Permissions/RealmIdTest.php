<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions;

use App\Domain\Permissions\RealmId;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class RealmIdTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $id = RealmId::create();

        static::assertInstanceOf(RealmId::class, $id);
    }
}
