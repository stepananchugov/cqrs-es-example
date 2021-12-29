<?php

declare(strict_types=1);

namespace App\Ui\Exception;

final class LogicException extends \LogicException
{
    public static function messageWithoutHandledStamp(): self
    {
        return new self('Message was sent to a bus but was not handled');
    }
}
