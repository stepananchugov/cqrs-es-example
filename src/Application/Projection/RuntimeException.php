<?php

declare(strict_types=1);

namespace App\Application\Projection;

use EventSauce\EventSourcing\Message;

class RuntimeException extends \RuntimeException
{
    public static function missingAggregateRootId(Message $message): self
    {
        return new self(sprintf(
            'A message with event %s must have an aggregate root ID',
            \get_class($message->event())
        ));
    }
}
