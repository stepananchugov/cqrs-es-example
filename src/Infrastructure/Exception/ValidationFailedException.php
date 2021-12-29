<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use GraphQL\Error\ClientAware;
use Symfony\Component\Messenger\Exception\ValidationFailedException as SymfonyValidationFailedException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationFailedException extends SymfonyValidationFailedException implements ClientAware
{
    public function __construct(object $violatingMessage, ConstraintViolationListInterface $violations)
    {
        parent::__construct($violatingMessage, $violations);
        $message = $violations->get(0)->getMessage();

        if (\is_string($message)) {
            $this->message = $message;
        }
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'validationFailed';
    }
}
