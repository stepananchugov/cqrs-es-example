<?php

declare(strict_types=1);

namespace App\Application\Exception;

use GraphQL\Error\ClientAware;

class ApiException extends \RuntimeException implements ClientAware
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
