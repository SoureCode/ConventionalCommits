<?php

namespace SoureCode\ConventionalCommits\Validator;

use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\Message\Message;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    private ValidatorInterface $validator;

    private ConfigurationLoader $configurationLoader;

    public function __construct(ValidatorInterface $validator, ConfigurationLoader $configurationLoader)
    {
        $this->validator = $validator;
        $this->configurationLoader = $configurationLoader;
    }

    public function validate(Message $message): void
    {
        $configuration = $this->configurationLoader->load();

        $violations = $this->validator->validate(
            $message,
            [
                new Constraints\Message(
                    header: new Constraints\Header(
                        typeMinLength: $configuration['type']['min'],
                        typeMaxLength: $configuration['type']['max'],
                        typeExtra: $configuration['type']['extra'],
                        typeValues: $configuration['type']['values'],
                        scopeMinLength: $configuration['scope']['min'],
                        scopeMaxLength: $configuration['scope']['max'],
                        scopeExtra: $configuration['scope']['extra'],
                        scopeValues: $configuration['scope']['values'],
                        scopeRequired: $configuration['scope']['required'],
                        descriptionMinLength: $configuration['description']['min'],
                        descriptionMaxLength: $configuration['description']['max'],
                    )
                ),
            ]
        );

        if (0 < $violations->count()) {
            throw new ValidationFailedException($message, $violations);
        }
    }
}
