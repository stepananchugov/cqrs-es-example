<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\ExpressionLanguage\ExpressionFunction\GraphQL;

use Overblog\GraphQLBundle\ExpressionLanguage\ExpressionFunction;

final class Arguments extends ExpressionFunction
{
    public function __construct($name = 'msarguments')
    {
        parent::__construct(
            $name,
            static function ($mapping, $data): string {
                return sprintf('$globalVariable->get(\'container\')->get(\'graphql.arguments_transformer\')->getArguments(%s, %s, $info)', $mapping, $data);
            }
        );
    }
}
