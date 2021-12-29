<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\MessageBus;

use App\Infrastructure\Exception\ValidationFailedException;
use EventSauce\EventSourcing\Message as EventSourcingMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ValidationStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationMiddleware implements MiddlewareInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof EventSourcingMessage) {
            $message = $message->event();
        }

        $stamp = $envelope->last(ValidationStamp::class);
        $groups = null;

        if ($stamp instanceof ValidationStamp) {
            $groups = $stamp->getGroups();
        }

        $violations = $this->validator->validate($message, null, $groups);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($message, $violations);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
