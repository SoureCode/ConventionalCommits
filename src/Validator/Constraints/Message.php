<?php

namespace SoureCode\ConventionalCommits\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Message extends Constraint
{
    public Header $header;

    public function __construct(
        Header $header,
        array $options = null,
        array $groups = null,
        $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->header = $header;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
