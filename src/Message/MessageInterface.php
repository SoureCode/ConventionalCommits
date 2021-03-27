<?php

namespace SoureCode\ConventionalCommits\Message;

use Stringable;

interface MessageInterface extends Stringable
{
    public function getHeader(): HeaderInterface;

    public function setHeader(HeaderInterface $header): self;

    public function getBody(): ?string;

    public function setBody(?string $body): self;

    /**
     * @return FooterInterface[]
     */
    public function getFooters(): array;

    /**
     * @param FooterInterface[] $footers
     */
    public function setFooters(array $footers): self;
}
