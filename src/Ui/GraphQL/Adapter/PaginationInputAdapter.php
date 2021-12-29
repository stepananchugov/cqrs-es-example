<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter;

use App\Application\Query\Share\Pagination;
use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Mapping\InputObject\PaginationInput;

final class PaginationInputAdapter implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return PaginationInput::class;
    }

    public function transform(object $input): Pagination
    {
        if (!$input instanceof PaginationInput) {
            throw InvalidArgumentException::inputTypeMismatch($input, PaginationInput::class);
        }

        if (null === $input->after) {
            return new Pagination($input->first, 0);
        }

        return Pagination::fromFirstAfter($input->first, $input->after);
    }
}
