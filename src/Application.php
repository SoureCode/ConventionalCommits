<?php

namespace SoureCode\ConventionalCommits;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        $version = $this->getVersion();
        $commit = $this->getCommit();

        if (!str_starts_with($commit, '@')) {
            $version .= sprintf('@%s', $commit);
        }

        parent::__construct('conventional-commits', $version);
    }

    public function getCommit(): string
    {
        return '@git-commit@';
    }

    public function getVersion(): string
    {
        return '0.1.0';
    }
}
