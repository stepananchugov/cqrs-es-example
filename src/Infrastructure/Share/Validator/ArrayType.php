<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ArrayType extends Constraint
{
    public string $message = 'Value {{ value }} with key {{ key }} should be of type {{ type }}. ';

    public array $types = [];

    public function __construct($options = null)
    {
        foreach ($options['types'] ?? [] as $type) {
            if (!\is_string($type)) {
                throw new UnexpectedTypeException($type, 'string');
            }
        }

        parent::__construct($options);
    }

    public function getRequiredOptions()
    {
        return ['types'];
    }
}
