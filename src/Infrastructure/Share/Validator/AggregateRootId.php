<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AggregateRootId extends Constraint
{
    public const AGGREGATE_ROOT_ID_NOT_FOUND = '1dfd4e50-0c8c-459d-a3ed-c88ca4adb3fd';

    public const AGGREGATE_ROOT_ID_FOUND = '3fb85a4d-3ace-4f2b-a3ee-c1eb05b6b2c3';

    public const SHOULD_EXIST = 'SHOULD_EXIST';

    public const SHOULD_NOT_EXIST = 'SHOULD_NOT_EXIST';

    private const ALLOWED_TYPES = [self::SHOULD_EXIST, self::SHOULD_NOT_EXIST];

    /**
     * @var string
     */
    public $type;

    public function __construct($options = null)
    {
        if (isset($options['type']) && !\in_array($options['type'], self::ALLOWED_TYPES, true)) {
            throw ValidatorException::badCheckType(
                $options['type'],
                self::ALLOWED_TYPES
            );
        }

        parent::__construct($options);

        if (null === $this->type) {
            $this->type = self::SHOULD_EXIST;
        }
    }

    public function getDefaultOption(): string
    {
        return 'type';
    }
}
