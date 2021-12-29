<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter\Permissions;

use App\Application\Command\Permissions\RevokePermissionCommand;
use App\Domain\Permissions\PolicyId;
use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Adapter\InputAdapterInterface;
use App\Ui\GraphQL\Mapping\InputObject\Permissions\RevokePermissionInput;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class RevokePermissionInputAdapter implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return RevokePermissionInput::class;
    }

    public function transform(object $input): SerializablePayload
    {
        if (!$input instanceof RevokePermissionInput) {
            throw InvalidArgumentException::inputTypeMismatch($input, RevokePermissionInput::class);
        }

        return new RevokePermissionCommand(
            $input->permissionId,
            $input->roleId,
            PolicyId::defaultPolicyId(),
        );
    }
}
