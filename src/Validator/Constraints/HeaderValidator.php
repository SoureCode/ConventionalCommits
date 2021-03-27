<?php

namespace SoureCode\ConventionalCommits\Validator\Constraints;

use SoureCode\ConventionalCommits\Message\HeaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class HeaderValidator extends ConstraintValidator
{
    public function validate($header, Constraint $constraint)
    {
        if (!$constraint instanceof Header) {
            throw new UnexpectedTypeException($constraint, Header::class);
        }

        if (!$header instanceof HeaderInterface) {
            return;
        }

        $validator = $this->context->getValidator()->inContext($this->context);

        $groups = $constraint->groups;

        $typeConstraints = [
            new NotBlank(),
            new Length(min: $constraint->typeMinLength, max: $constraint->typeMaxLength),
        ];

        if (!$constraint->typeExtra) {
            $typeConstraints[] = new Choice(choices: $constraint->typeValues);
        }

        $validator->atPath('type')->validate($header->getType(), $typeConstraints, $groups);

        $scopeConstraints = [
            new Length(min: $constraint->scopeMinLength, max: $constraint->scopeMaxLength),
        ];

        if ($constraint->scopeRequired) {
            array_unshift($scopeConstraints, new NotBlank());
        }

        if (!$constraint->scopeExtra) {
            $scopeConstraints[] = new Choice(choices: $constraint->scopeValues);
        }

        $validator->atPath('scope')->validate($header->getScope(), $scopeConstraints, $groups);

        $descriptionConstraints = [
            new NotBlank(),
            new Length(min: $constraint->descriptionMinLength, max: $constraint->descriptionMaxLength),
        ];

        $validator->atPath('description')->validate($header->getDescription(), $descriptionConstraints, $groups);

    }
}
