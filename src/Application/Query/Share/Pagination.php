<?php

declare(strict_types=1);

namespace App\Application\Query\Share;

use App\Application\Exception\InvalidArgumentException;
use App\Infrastructure\Share\DBAL\QueryModifier;
use Doctrine\DBAL\Query\QueryBuilder;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

class Pagination implements QueryModifier, SerializablePayload
{
    public const DEFAULT_LIMIT = 10;

    private int $limit;

    private int $offset;

    public function __construct(int $limit, int $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public static function fromFirstAfter(int $first, string $after): self
    {
        $decoded = base64_decode($after, true);

        if (false === $decoded) {
            throw new InvalidArgumentException(sprintf(
                'Could not decode `%s` as "after" pagination parameter',
                $after
            ));
        }

        $offset = (int) $decoded;

        if ((string) $offset !== $decoded) {
            throw new InvalidArgumentException(sprintf(
                'Could not decode "after" pagination parameter `%s` into an integer',
                $after
            ));
        }

        return new self($first, $offset);
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function modify(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->setFirstResult($this->offset);
        $queryBuilder->setMaxResults($this->limit);
    }

    public function toPayload(): array
    {
        return [
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }

    /**
     * @return Pagination
     */
    public static function fromPayload(array $payload): self
    {
        return new self($payload['limit'], $payload['offset']);
    }
}
