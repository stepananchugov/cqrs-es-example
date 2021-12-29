<?php

declare(strict_types=1);

namespace App\Application\Query\Share;

trait PaginationAwareTrait
{
    private ?Pagination $pagination = null;

    public function withPagination(Pagination $pagination): self
    {
        $clone = clone $this;
        $clone->pagination = $pagination;

        return $clone;
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }
}
