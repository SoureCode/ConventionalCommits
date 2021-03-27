<?php

namespace SoureCode\ConventionalCommits\Git;

use ArrayIterator;
use GitWrapper\GitWorkingCopy;
use IteratorAggregate;
use Symfony\Component\String\AbstractString;
use function Symfony\Component\String\u;
use Traversable;

final class GitCommits implements IteratorAggregate
{
    private GitWorkingCopy $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
    }

    public function getIterator(): Traversable
    {
        $commits = $this->all();

        return new ArrayIterator($commits);
    }

    /**
     * @return string[]
     *
     * @api
     */
    public function all(): array
    {
        return $this->fetchCommits();
    }

    /**
     * Fetches the Commits via the `git log` command.
     *
     * @return string[]
     *
     * @api
     */
    public function fetchCommits(): array
    {
        $output = $this->gitWorkingCopy->log(
            [
                'format' => '%H',
            ]
        );

        $commits = u($output)->trim()->split("/\r?\n/");

        return $commits;
    }

    public function getRange(string $first, string $second): GitCommitRange
    {
        $output = $this->gitWorkingCopy->log(
            [
                'format=%H' => '',
            ],
            $first.'..'.$second
        );

        $commits = array_map(
            fn (AbstractString $commit) => $this->get((string) $commit->trim()),
            array_reverse(
                array_merge(
                    u($output)->trim()->split("\n"),
                    [u($first)],
                )
            )
        );

        return new GitCommitRange($commits);
    }

    public function get(string $hash): GitCommit
    {
        $formatLines = [
            'Hash: %H',
            'Author: %an <%ae>',
            'AuthorDate: %aI',
            'Committer: %cn <%ce>',
            'CommitterDate: %cI',
            'Subject: %s',
            'Body: %b',
        ];

        $format = implode('%n', $formatLines);

        $output = $this->gitWorkingCopy->show(
            $hash,
            [
                'format='.$format => '',
                'no-patch' => true,
            ]
        );

        $commit = $this->parseShowOutput($output);

        return $commit;
    }

    private function parseShowOutput(string $text): GitCommit
    {
        /**
         * @var AbstractString[] $lines
         */
        $lines = array_map(static fn (AbstractString $item) => $item->trim(), u($text)->trim()->split("\n"));
        $items = [];

        $captureBody = false;

        foreach ($lines as $line) {
            if (!$captureBody) {
                $split = $line->split(':');
                $key = array_shift($split);
                $value = u(implode(':', $split))->trim();

                if ($key->equalsTo('Body')) {
                    $captureBody = true;
                }

                $items[(string) $key->camel()] = (string) $value;
            } else {
                $items['body'] .= sprintf("\n%s", $value);
            }
        }

        return new GitCommit(...$items);
    }
}
