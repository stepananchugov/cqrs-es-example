<?php

declare(strict_types=1);

namespace App\Infrastructure\Permissions\Validator;

use App\Domain\Permissions\Permission;
use App\Infrastructure\Permissions\Configuration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class PermissionIdValidator extends ConstraintValidator
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PermissionId) {
            throw new UnexpectedTypeException($constraint, PermissionId::class);
        }

        if (null === $value || Permission::PERMISSION_ALL === $value) {
            return;
        }

        if (!\is_string($value) || !$this->configuration->hasPermission($value)) {
            $this->context
                ->buildViolation('Permission ID is invalid or does not exist')
                ->setInvalidValue($value)
                ->setCode(PermissionId::INVALID_PERMISSION)
                ->addViolation()
            ;
        }
    }
}
