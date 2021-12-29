<?php

declare(strict_types=1);

namespace App\Infrastructure\Permissions\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class RoleId extends Constraint
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_TRANSLATOR = 'ROLE_TRANSLATOR';
    public const ROLE_EVENT_MANAGER = 'ROLE_EVENT_MANAGER';
    public const ROLE_MARKETING_MANAGER = 'ROLE_MARKETING_MANAGER';
    public const ROLE_CIRCUIT_MANAGER = 'ROLE_CIRCUIT_MANAGER';

    public const INVALID_ROLE = 'e08c2143-453f-4d0d-9fb6-05c185d4ea11';

    public function validatedBy()
    {
        return RoleIdValidator::class;
    }
}
