<?php

namespace SoureCode\ConventionalCommits\Git;

use function count;
use InvalidArgumentException;
use Symfony\Component\String\AbstractUnicodeString;
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
     * @return GitCommit[]
     */
    public function fetchRanges(string $text): array
    {
        $ranges = u($text)->split(',');

        $commits = $this->doFetchRanges($ranges);

        return $commits;
    }

    /**
     * @param AbstractUnicodeString[] $ranges
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
            $hashes = $range->split('...');
            $hashesCount = count($hashes);

            if (1 === $hashesCount) {
                $commit = $gitCommits->get((string) $hashes[0]);
                $commits[] = [$commit->getHash()];
            } elseif (2 === $hashesCount) {
                [$first, $last] = $hashes;

                $commits[] = $gitCommits->fetchRange((string) $first, (string) $last);
            } else {
                throw new InvalidArgumentException(sprintf('Invalid commit range: "%s"', (string) $range));
            }
        }

        /**
         * @var string[] $commits
         */
        $commits = array_merge(...$commits);

        return array_map(static fn (string $commit) => $gitCommits->get($commit), $commits);
    }
}
