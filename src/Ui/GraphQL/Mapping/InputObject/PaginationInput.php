<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\InputObject;

use App\Application\Query\Share\Pagination;
use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Input
 */
final class PaginationInput
{
    /**
     * @GQL\Field(type="String")
     */
    public ?string $after = null;

    /**
     * @GQL\Field(type="Int")
     * @GQL\Description("Defaults to 10")
     * @TODO: ME-4799 Move to envs
     */
    public int $first = Pagination::DEFAULT_LIMIT;
}
