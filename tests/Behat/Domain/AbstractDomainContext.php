<?php

declare(strict_types=1);

namespace App\Tests\Behat\Domain;

use Behat\Behat\Context\Context;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

abstract class AbstractDomainContext implements Context
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    protected function handleMessage(object $query)
    {
        $message = $this->messageBus->dispatch($query);
        $handledStamp = $message->last(HandledStamp::class);

        if (!$handledStamp instanceof HandledStamp) {
            throw new \Exception('Message was not handled');
        }

        return $handledStamp->getResult();
    }
}
