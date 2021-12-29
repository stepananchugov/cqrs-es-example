<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Formatter;

use Overblog\GraphQLBundle\Event\ErrorFormattingEvent;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationErrorFormatter
{
    public function onErrorFormatting(ErrorFormattingEvent $event): void
    {
        $exception = $this->getValidationFailedException($event->getError());

        if (null === $exception) {
            return;
        }

        $state = [];
        $code = [];

        foreach ($exception->getViolations() as $violation) {
            /** @var ConstraintViolationInterface $violation */
            $propertyPath = $violation->getPropertyPath();

            $state[$propertyPath] = $violation->getMessage();
            $code[$propertyPath] = $violation->getCode();
        }

        $formattedError = $event->getFormattedError();
        $formattedError->offsetSet('state', $state);
        $formattedError->offsetSet('code', $code);
    }

    private function getValidationFailedException(\Exception $exception): ?ValidationFailedException
    {
        if ($exception instanceof ValidationFailedException) {
            return $exception;
        }

        $exception = $exception->getPrevious();

        if ($exception instanceof \Exception) {
            return $this->getValidationFailedException($exception);
        }

        return null;
    }
}
