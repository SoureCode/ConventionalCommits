<?php

namespace SoureCode\ConventionalCommits;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('conventional-commits', '0.1.0-dev');
    }
}
