<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class UrlException extends \InvalidArgumentException
{
    public static function invalidUrl(string $url): self
    {
        return new self(sprintf(
            '`%s` is not a valid URL',
            $url
        ));
    }
}
