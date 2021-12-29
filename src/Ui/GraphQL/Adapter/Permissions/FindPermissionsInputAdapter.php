<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter\Permissions;

use App\Application\Query\Permissions\ListPermissionsQuery;
use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Adapter\InputAdapterInterface;
use App\Ui\GraphQL\Mapping\InputObject\Permissions\FindPermissionsInput;

class FindPermissionsInputAdapter implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return FindPermissionsInput::class;
    }

    public function transform(object $input): ListPermissionsQuery
    {
        if (!$input instanceof FindPermissionsInput) {
            throw InvalidArgumentException::inputTypeMismatch($input, FindPermissionsInput::class);
        }

        $query = new ListPermissionsQuery();

        if (null !== $input->roleId) {
            $query = $query->withRoleIds([$input->roleId]);
        }

        return $query;
    }
}
