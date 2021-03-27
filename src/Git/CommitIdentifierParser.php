<?php

namespace SoureCode\ConventionalCommits\Git;

use function count;
use GitWrapper\GitWrapper;
use InvalidArgumentException;
use function Symfony\Component\String\u;

class CommitIdentifierParser
{
    private GitWrapper $git;

    public function __construct(GitWrapper $git)
    {
        $this->git = $git;
    }

    public function parse(string $text): GitCommitRangeSet
    {
        $ranges = u($text)->split(',');

        $rangeSets = $this->parseRanges($ranges);

        return $rangeSets;
    }

    private function parseRanges(array $ranges): GitCommitRangeSet
    {
        $git = $this->git->workingCopy(getcwd());
        $commits = new GitCommits($git);

        $rangeSets = [];

        foreach ($ranges as $range) {
            $hashes = u($range)->split('-');
            $hashesCount = count($hashes);

            if (1 === $hashesCount) {
                $commit = $commits->get((string) $hashes[0]);

                $rangeSets[] = new GitCommitRange([$commit]);
            } elseif (2 === $hashesCount) {
                $first = $hashes[0];
                $last = $hashes[1];

                $rangeSets[] = $commits->getRange((string) $first, (string) $last);
            } else {
                throw new InvalidArgumentException(sprintf('Invalid commit range: "%s"', $range));
            }
        }

        return new GitCommitRangeSet($rangeSets);
    }
}
