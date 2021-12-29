<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter\Permissions;

use App\Application\Command\Permissions\GrantPermissionCommand;
use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Adapter\InputAdapterInterface;
use App\Ui\GraphQL\Mapping\InputObject\Permissions\GrantPermissionInput;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

class GrantPermissionsInputAdapter implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return GrantPermissionInput::class;
    }

    public function transform(object $input): SerializablePayload
    {
        if (!$input instanceof GrantPermissionInput) {
            throw InvalidArgumentException::inputTypeMismatch($input, GrantPermissionInput::class);
        }

        return new GrantPermissionCommand($input->permissionId, $input->roleId);
    }
}
