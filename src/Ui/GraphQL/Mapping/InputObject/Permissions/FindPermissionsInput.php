<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\InputObject\Permissions;

use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Input("FindPermissionsInput")
 */
final class FindPermissionsInput
{
    /**
     * @GQL\Field(type="String")
     */
    public ?string $roleId = null;

    /**
     * @GQL\Field(type="String")
     */
    public ?string $username = null;
}
