<?php

namespace SoureCode\ConventionalCommits\Commit;

use function count;
use InvalidArgumentException;
use function Symfony\Component\String\u;

final class Footer
{
    private const EXPRESSION = '/^(?<key>[a-zA-Z-]+|BREAKING CHANGE)(?<separator>: | #)(?<value>.+)$/sm';

    public const SEPARATOR_TYPE_COLON = 'colon';

    public const SEPARATOR_TYPE_HASH = 'hash';

    private string $separatorType;

    private string $key;

    private string $value;

    private function __construct(string $type, string $key, string $value)
    {
        $this->separatorType = $type;
        $this->key = $key;
        $this->value = $value;
    }

    public static function fromString(string $text): self
    {
        $matches = u($text)->match(self::EXPRESSION);

        if (0 === count($matches)) {
            throw new InvalidArgumentException('Invalid footer format.');
        }

        $separator = $matches['separator'];

        if (': ' === $separator) {
            $separator = self::SEPARATOR_TYPE_COLON;
        } elseif (' #' === $separator) {
            $separator = self::SEPARATOR_TYPE_HASH;
        }

        return new self(
            $separator,
            $matches['key'],
            $matches['value'],
        );
    }

    public function getSeparatorType(): string
    {
        return $this->separatorType;
    }

    public function setSeparatorType(string $separatorType): self
    {
        return new self(
            $separatorType,
            $this->key,
            $this->value
        );
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        return new self(
            $this->separatorType,
            $key,
            $this->value
        );
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        return new self(
            $this->separatorType,
            $this->key,
            $value
        );
    }

    public function toString(): string
    {
        $footerParts = [$this->key];

        if (self::SEPARATOR_TYPE_COLON === $this->separatorType) {
            $footerParts[] = ': ';
        } elseif (self::SEPARATOR_TYPE_HASH === $this->separatorType) {
            $footerParts[] = ' #';
        }

        $footerParts[] = $this->value;

        return implode('', $footerParts);
    }
}
