<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use App\Domain\Shared\ObjectId;

final class PolicyId extends ObjectId
{
    private const MOTORSPORT_POLICY_ID = 'afa0ff8b-8a3d-44d9-8298-3d82fcc0a3b3';

    public static function defaultPolicyId(): self
    {
        return self::fromString(self::MOTORSPORT_POLICY_ID);
    }
}
