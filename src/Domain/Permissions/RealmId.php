<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use App\Domain\Shared\ObjectId;

final class RealmId extends ObjectId
{
    private const MOTORSPORT_REALM_ID = '71f79c34-98b8-4564-bafb-dc912aaee743';

    public static function defaultRealmId(): self
    {
        return self::fromString(self::MOTORSPORT_REALM_ID);
    }
}
