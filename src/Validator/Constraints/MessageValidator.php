<?php

namespace SoureCode\ConventionalCommits\Validator\Constraints;

use SoureCode\ConventionalCommits\Message\MessageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MessageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Message) {
            throw new UnexpectedTypeException($constraint, Message::class);
        }

        if (!$value instanceof MessageInterface) {
            return;
        }

        $validator = $this->context->getValidator()->inContext($this->context);

        $groups = $constraint->groups;

        $validator->atPath('header')->validate(
            $value->getHeader(),
            [
                $constraint->header,
            ],
            $groups
        );
    }
}
