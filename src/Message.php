<?php

namespace SoureCode\ConventionalCommits;

use function count;
use Symfony\Component\String\AbstractString;
use function Symfony\Component\String\u;
use Symfony\Component\String\UnicodeString;

final class Message
{
    private const START_WITH_FOOTER_EXPRESSION = '/^(?<key>[a-zA-Z-]+|BREAKING CHANGE)(?<separator>: | #)/m';

    private Header $header;

    private ?string $body;

    /**
     * @var Footer[]
     */
    private array $footers;

    /**
     * @param Footer[] $footers
     */
    private function __construct(Header $header, ?string $body = null, array $footers = [])
    {
        $this->header = $header;
        $this->body = $body;
        $this->footers = $footers;
    }

    public static function fromString(string $message): self
    {
        $text = u($message)
            ->trim()
            ->replaceMatches("/\r?\n/", "\n");

        $lines = array_map(
            static fn (AbstractString $string): string => $string->toString(),
            $text->split("\n")
        );

        /**
         * @var string $firstLine
         */
        $firstLine = array_shift($lines);

        $header = Header::fromString($firstLine);

        [$body, $footers] = self::parseBodyAndFooters($lines);

        return new self(
            $header,
            $body,
            $footers,
        );
    }

    /**
     * @param string[] $lines
     * @psalm-suppress RedundantCondition
     */
    private static function parseBodyAndFooters(array $lines): array
    {
        $body = u();
        /**
         * @var Footer[] $footers
         */
        $footers = [];

        $addFooter = static function (UnicodeString $string) use (&$footers): void {
            $footers[] = Footer::fromString($string->trim()->toString());
        };

        $currentFooter = u();
        $captureFooter = false;
        foreach ($lines as $line) {
            $matches = u($line)->match(self::START_WITH_FOOTER_EXPRESSION);

            if (count($matches) > 0) {
                if ($captureFooter) {
                    $addFooter($currentFooter);
                    $currentFooter = u();
                }

                $captureFooter = true;

                $currentFooter = $currentFooter->append($line);
            } elseif ($captureFooter) {
                $currentFooter = $currentFooter->append("\n", $line);
            } elseif (!$captureFooter) {
                $body = $body->append("\n", $line);
            }
        }

        if ($currentFooter->length() > 0) {
            $addFooter($currentFooter);
        }

        $body = $body->trim();

        return [
            $body->length() > 0 ? $body->toString() : null,
            $footers,
        ];
    }

    public function getHeader(): Header
    {
        return $this->header;
    }

    public function setHeader(Header $header): self
    {
        return new self(
            $header,
            $this->body,
            $this->footers,
        );
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        return new self(
            $this->header,
            $body,
            $this->footers,
        );
    }

    public function toString(): string
    {
        $messageParts = [
            $this->header->toString(),
        ];

        if ($this->body) {
            $messageParts[] = $this->body;
        }

        if (count($this->footers) > 0) {
            $footerParts = [];

            foreach ($this->footers as $footer) {
                $footerParts[] = $footer->toString();
            }

            $messageParts[] = implode("\n", $footerParts);
        }

        return implode("\n\n", $messageParts);
    }

    /**
     * @return Footer[]
     */
    public function getFooters(): array
    {
        return $this->footers;
    }

    /**
     * @param Footer[] $footers
     */
    public function setFooters(array $footers): self
    {
        return new self(
            $this->header,
            $this->body,
            $footers,
        );
    }
}
