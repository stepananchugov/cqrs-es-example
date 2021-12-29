<?php

declare(strict_types=1);

namespace App\Tests\Domain\Permissions;

use App\Domain\Permissions\PolicyId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Domain\Permissions\PolicyId
 */
class PolicyIdTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $id = PolicyId::create();

        static::assertInstanceOf(PolicyId::class, $id);
    }
}
