<?php

namespace SoureCode\ConventionalCommits\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Header extends Constraint
{
    public int $typeMinLength = 3;
    public int $typeMaxLength = 10;
    public bool $typeExtra = false;

    /**
     * @var string[]
     */
    public array $typeValues = ['feat', 'fix', 'test', 'chore', 'docs', 'refactor', 'revert'];

    public int $scopeMinLength = 3;
    public int $scopeMaxLength = 10;
    public bool $scopeExtra = true;
    public bool $scopeRequired = false;

    /**
     * @var string[]
     */
    public array $scopeValues = [];

    public int $descriptionMinLength = 5;
    public int $descriptionMaxLength = 50;

    public function __construct(
        array $options = null,
        int $typeMinLength = null,
        int $typeMaxLength = null,
        bool $typeExtra = null,
        array $typeValues = null,
        int $scopeMinLength = null,
        int $scopeMaxLength = null,
        bool $scopeExtra = null,
        array $scopeValues = null,
        bool $scopeRequired = null,
        int $descriptionMinLength = null,
        int $descriptionMaxLength = null,
        array $groups = null,
        $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->typeMinLength = $typeMinLength ?? $this->typeMinLength;
        $this->typeMaxLength = $typeMaxLength ?? $this->typeMaxLength;
        $this->typeExtra = $typeExtra ?? $this->typeExtra;
        $this->typeValues = $typeValues ?? $this->typeValues;
        $this->scopeMinLength = $scopeMinLength ?? $this->scopeMinLength;
        $this->scopeMaxLength = $scopeMaxLength ?? $this->scopeMaxLength;
        $this->scopeExtra = $scopeExtra ?? $this->scopeExtra;
        $this->scopeRequired = $scopeRequired ?? $this->scopeRequired;
        $this->scopeValues = $scopeValues ?? $this->scopeValues;
        $this->descriptionMinLength = $descriptionMinLength ?? $this->descriptionMinLength;
        $this->descriptionMaxLength = $descriptionMaxLength ?? $this->descriptionMaxLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
