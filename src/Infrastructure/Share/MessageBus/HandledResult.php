<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\MessageBus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class HandledResult
{
    /**
     * @var mixed
     */
    private $result;

    public function __construct(Envelope $envelope)
    {
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp instanceof HandledStamp) {
            throw new \LogicException('No handled result available');
        }

        $this->result = $handledStamp->getResult();
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
