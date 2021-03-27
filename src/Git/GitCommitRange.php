<?php

namespace SoureCode\ConventionalCommits\Git;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class GitCommitRange implements IteratorAggregate
{
    /**
     * @var GitCommit[]
     */
    private array $commits;

    public function __construct(array $commits)
    {
        $this->commits = $commits;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->commits);
    }
}
