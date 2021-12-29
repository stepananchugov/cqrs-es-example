<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Query\Exception;

use Doctrine\DBAL\Query\QueryBuilder;

final class UnsupportedQueryException extends \InvalidArgumentException
{
    private const TYPE_NAME_MAP = [
        QueryBuilder::SELECT => 'SELECT',
        QueryBuilder::DELETE => 'DELETE',
        QueryBuilder::UPDATE => 'UPDATE',
        QueryBuilder::INSERT => 'INSERT',
    ];

    public static function invalidQueryBuilderType(int $type): self
    {
        if (!\array_key_exists($type, self::TYPE_NAME_MAP)) {
            return new self(sprintf(
                'Query builder type `%s` is invalid',
                $type
            ));
        }

        return new self(sprintf(
            'Only `SELECT` query builder type is supported, `%s` passed instead.',
            self::TYPE_NAME_MAP[$type],
        ));
    }
}
