<?php

namespace SoureCode\ConventionalCommits\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Message extends Constraint
{

    public Header $header;

    public function __construct(
        array $options = null,
        Header $header = null,
        array $groups = null,
        $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->header = $header ?? $this->header;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
