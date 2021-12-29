<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\InputObject;

use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Input
 */
final class DateTimeRangeInput
{
    /**
     * @GQL\Field(type="DateTime!")
     */
    public \DateTimeImmutable $from;

    /**
     * @GQL\Field(type="DateTime!")
     */
    public \DateTimeImmutable $to;
}
