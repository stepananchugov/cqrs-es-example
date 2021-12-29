<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ArrayTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ArrayType) {
            throw new UnexpectedTypeException($constraint, ArrayType::class);
        }

        if (!\is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $valueKey => $valueItem) {
            foreach ($constraint->types as $type) {
                if ($this->isRightType($valueItem, $type)) {
                    continue 2;
                }
            }
            $violationBuilder = $this->context->buildViolation($constraint->message);
            $violationBuilder->setParameter('{{ key }}', (string) $valueKey);
            $violationBuilder->setParameter('{{ value }}', $this->formatValue($valueItem));
            $violationBuilder->setParameter('{{ type }}', implode('|', $constraint->types));
            $violationBuilder->setCode(Type::INVALID_TYPE_ERROR);
            $violationBuilder->addViolation();
        }
    }

    /**
     * @param mixed $value
     */
    private function isRightType($value, string $type): bool
    {
        $type = strtolower($type);
        $type = 'boolean' === $type ? 'bool' : $type;
        $isFunction = 'is_'.$type;

        return (\is_callable($isFunction) && $isFunction($value)) || ($value instanceof $type);
    }
}
