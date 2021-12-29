<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\MessageBus;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class PaginatedStamp implements StampInterface
{
    private array $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function result(): array
    {
        return $this->result;
    }
}
