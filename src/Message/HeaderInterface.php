<?php

namespace SoureCode\ConventionalCommits\Message;

use Stringable;

interface HeaderInterface extends Stringable
{
    public function getType(): string;

    public function setType(string $type): self;

    public function getScope(): ?string;

    public function setScope(?string $scope): self;

    public function getDescription(): string;

    public function setDescription(string $description): self;

    public function isBreakingChange(): bool;

    public function setBreakingChange(bool $isBreakingChange): self;
}
