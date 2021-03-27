<?php

namespace SoureCode\ConventionalCommits\Message;

use InvalidArgumentException;
use function count;
use function Symfony\Component\String\u;

class Header implements HeaderInterface
{
    private const EXPRESSION = '/^(?<type>[a-zA-Z]+)(\((?<scope>[a-zA-Z]+)\))?(?<breaking>!)?: (?<description>.+)$/m';

    private string $type;

    private ?string $scope;

    private bool $isBreakingChange;

    private string $description;

    private function __construct(string $type, string $description, ?string $scope = null, bool $isBreakingChange = false)
    {
        $this->type = $type;
        $this->description = $description;
        $this->scope = $scope;
        $this->isBreakingChange = $isBreakingChange;
    }

    public static function fromString(string $text): self
    {
        $matches = u($text)->match(self::EXPRESSION);

        if (0 === count($matches)) {
            throw new InvalidArgumentException('Invalid header format.');
        }

        return new self(
            $matches['type'],
            $matches['description'],
            $matches['scope'] ?? null,
            $matches['breaking'] ?? false,
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        return new self(
            $type,
            $this->description,
            $this->scope,
            $this->isBreakingChange,
        );
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        return new self(
            $this->type,
            $this->description,
            $scope,
            $this->isBreakingChange,
        );
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $description,
            $this->scope,
            $this->isBreakingChange,
        );
    }

    public function isBreakingChange(): bool
    {
        return $this->isBreakingChange;
    }

    public function setBreakingChange(bool $isBreakingChange): self
    {
        return new self(
            $this->type,
            $this->description,
            $this->scope,
            $isBreakingChange,
        );
    }

    public function __toString(): string
    {
        $headerParts = [$this->type];

        if ($this->scope) {
            $headerParts[] = sprintf('(%s)', $this->scope);
        }

        if ($this->isBreakingChange) {
            $headerParts[] = '!';
        }

        $headerParts[] = ': ';
        $headerParts[] = $this->description;

        return implode('', $headerParts);
    }
}
