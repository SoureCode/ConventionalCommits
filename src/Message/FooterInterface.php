<?php

namespace SoureCode\ConventionalCommits\Message;

use Stringable;

interface FooterInterface extends Stringable
{
    public function getSeparatorType(): string;

    public function setSeparatorType(string $separatorType): self;

    public function getKey(): string;

    public function setKey(string $key): self;

    public function getValue(): string;

    public function setValue(string $value): self;
}
