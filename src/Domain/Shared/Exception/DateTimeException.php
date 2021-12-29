<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class DateTimeException extends \Exception
{
    public static function badFormat(string $format, string $dateTime = null): self
    {
        return new self(sprintf(
            'Cannot create a date for \'%s\' with format \'%s\'',
            $dateTime,
            $format
        ));
    }
}
