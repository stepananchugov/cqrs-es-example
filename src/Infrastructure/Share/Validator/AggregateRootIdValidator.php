<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Validator;

use EventSauce\EventSourcing\AggregateRootId as RootId;
use EventSauce\EventSourcing\MessageRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AggregateRootIdValidator extends ConstraintValidator
{
    private ?MessageRepository $messageRepository = null;

    public function __construct(MessageRepository $messageRepository = null)
    {
        $this->messageRepository = $messageRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AggregateRootId) {
            throw new UnexpectedTypeException($constraint, AggregateRootId::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof RootId) {
            throw ValidatorException::notAnAggregateRootId($value);
        }

        if (!$this->messageRepository instanceof MessageRepository) {
            return;
        }

        $generator = $this->messageRepository->retrieveAll($value);
        $aggregateExists = $generator->valid();

        if (AggregateRootId::SHOULD_EXIST === $constraint->type && !$aggregateExists) {
            $this->context
                ->buildViolation('Expected aggregate ID to exist, but it was not found')
                ->setInvalidValue($value->toString())
                ->setCode(AggregateRootId::AGGREGATE_ROOT_ID_NOT_FOUND)
                ->addViolation()
            ;
        }

        if (AggregateRootId::SHOULD_NOT_EXIST === $constraint->type && $aggregateExists) {
            $this->context
                ->buildViolation('Aggregate ID should not exist, but it was found')
                ->setInvalidValue($value->toString())
                ->setCode(AggregateRootId::AGGREGATE_ROOT_ID_FOUND)
                ->addViolation()
            ;
        }
    }
}
