<?php

declare(strict_types=1);

namespace App\Infrastructure\Permissions\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class PermissionId extends Constraint
{
    public const INVALID_PERMISSION = '6b8acdd6-1e9b-4cd2-a617-6b654a400fea';

    public function validatedBy()
    {
        return PermissionIdValidator::class;
    }
}
