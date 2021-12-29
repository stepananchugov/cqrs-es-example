<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\InputObject\Permissions;

use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Input
 */
final class RevokePermissionInput
{
    /**
     * @GQL\Field(type="String!")
     */
    public string $permissionId;

    /**
     * @GQL\Field(type="String!")
     */
    public string $roleId;
}
