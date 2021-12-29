<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\DBAL;

use App\Infrastructure\Share\Query\Exception\UnsupportedQueryException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;

class Table
{
    private Connection $connection;

    private string $tableName;

    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    public function insert(array $data, array $types = []): int
    {
        return $this->connection->insert($this->tableName, $data, $types);
    }

    public function update(array $data, array $identifier, array $types = []): int
    {
        return $this->connection->update($this->tableName, $data, $identifier, $types);
    }

    public function delete(array $identifier, array $types = []): int
    {
        return $this->connection->delete($this->tableName, $identifier, $types);
    }

    public function match(QueryModifier $queryModifier): ResultStatement
    {
        $qb = $this->getQuery($queryModifier);
        $qbType = $qb->getType();

        if (QueryBuilder::SELECT !== $qbType) {
            throw UnsupportedQueryException::invalidQueryBuilderType($qbType);
        }

        return $this->connection->executeQuery(
            $qb->getSQL(),
            $qb->getParameters(),
            $qb->getParameterTypes()
        );
    }

    private function getQuery(QueryModifier $queryModifier): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $queryModifier->modify($qb);

        return $qb;
    }
}
