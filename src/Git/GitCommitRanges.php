<?php

namespace SoureCode\ConventionalCommits\Git;

use function count;
use InvalidArgumentException;
use function Symfony\Component\String\u;
use Symplify\GitWrapper\GitCommit;
use Symplify\GitWrapper\GitWrapper;

class GitCommitRanges
{
    private GitWrapper $gitWrapper;

    private string $workingDirectory;

    public function __construct(GitWrapper $git, string $workingDirectory)
    {
        $this->gitWrapper = $git;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @param string $text
     *
     * @return GitCommit[]
     */
    public function fetchRanges(string $text): array
    {
        $ranges = u($text)->split(',');

        $commits = $this->doFetchRanges($ranges);

        return $commits;
    }

    /**
     * @param string[] $ranges
     *
     * @return GitCommit[]
     */
    private function doFetchRanges(array $ranges): array
    {
        $git = $this->gitWrapper->workingCopy($this->workingDirectory);
        $gitCommits = $git->commits();

        /**
         * @var string[][] $commits
         */
        $commits = [];

        foreach ($ranges as $range) {
            $hashes = u($range)->split('-');
            $hashesCount = count($hashes);

            if (1 === $hashesCount) {
                $commits[] = [$hashes[0]];
            } elseif (2 === $hashesCount) {
                [$first, $last] = $hashes;

                $commits[] = $gitCommits->fetchRange((string) $first, (string) $last);
            } else {
                throw new InvalidArgumentException(sprintf('Invalid commit range: "%s"', $range));
            }
        }

        /**
         * @var string[] $commits
         */
        $commits = array_merge(...$commits);

        return array_map(static fn (string $commit) => $gitCommits->get($commit), $commits);
    }
}
