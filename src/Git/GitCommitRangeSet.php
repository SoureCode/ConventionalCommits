<?php

namespace SoureCode\ConventionalCommits\Git;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class GitCommitRangeSet implements IteratorAggregate
{
    /**
     * @var GitCommitRange[]
     */
    private array $ranges;

    /**
     * @param GitCommitRange[] $ranges
     */
    public function __construct(array $ranges)
    {
        $this->ranges = $ranges;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ranges);
    }
}
