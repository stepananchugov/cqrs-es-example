<?php

declare(strict_types=1);

namespace App\Application\Exception;

use GraphQL\Error\ClientAware;

final class InvalidArgumentException extends \InvalidArgumentException implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'api';
    }
}
