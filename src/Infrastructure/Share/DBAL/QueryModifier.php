<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;

interface QueryModifier
{
    public function modify(QueryBuilder $queryBuilder): void;
}
