<?php

declare(strict_types=1);

namespace App\Application\Query\Share;

interface PaginationAware
{
    public function withPagination(Pagination $pagination): self;

    public function pagination(): ?Pagination;
}
