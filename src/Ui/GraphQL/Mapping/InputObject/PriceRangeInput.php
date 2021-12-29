<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Mapping\InputObject;

use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Input
 */
final class PriceRangeInput
{
    /**
     * @GQL\Field(type="Int")
     * @GQL\Description("Minimum amount, in cents")
     */
    public ?int $min = null;

    /**
     * @GQL\Field(type="Int")
     * @GQL\Description("Maximum amount, in cents")
     */
    public ?int $max = null;

    /**
     * @GQL\Field(type="String!")
     * @GQL\Description("3-letter code")
     */
    public ?string $currencyCode = null;
}
