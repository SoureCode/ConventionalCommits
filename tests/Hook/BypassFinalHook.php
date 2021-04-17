<?php

namespace SoureCode\ConventionalCommits\Tests\Hook;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

class BypassFinalHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
