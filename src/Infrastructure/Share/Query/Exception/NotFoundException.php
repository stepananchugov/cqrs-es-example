<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Query\Exception;

use GraphQL\Error\ClientAware;

final class NotFoundException extends \Exception implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'not found';
    }
}
