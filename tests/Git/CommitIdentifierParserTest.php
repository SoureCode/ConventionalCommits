<?php

namespace SoureCode\ConventionalCommits\Tests\Git;

use GitWrapper\GitWrapper;
use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Git\CommitIdentifierParser;

class CommitIdentifierParserTest extends TestCase
{
    public function testParseSimple()
    {
        // Arrange
        $gitWrapper = new GitWrapper();
        $commitIdentifierParser = new CommitIdentifierParser($gitWrapper);

        // Act
        $actual = $commitIdentifierParser->parse('8d0536a13d17a0cc7e0ceb0cc3555a66bb2d9a2d');

        // Assert
        $sets = iterator_to_array($actual->getIterator());

        self::assertCount(1, $actual);
        self::assertCount(1, $sets[0]);
    }

    public function testParseRange()
    {
        // Arrange
        $gitWrapper = new GitWrapper();
        $commitIdentifierParser = new CommitIdentifierParser($gitWrapper);

        // Act
        $actual = $commitIdentifierParser->parse('8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a-84c516b46713e139ae7c7560aa69f96ae3bb6700');

        // Assert
        $sets = iterator_to_array($actual->getIterator());
        $range = iterator_to_array($sets[0]);

        self::assertCount(1, $actual);
        self::assertCount(3, $sets[0]);
        self::assertSame('8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a', $range[0]->getHash());
        self::assertSame('8d0536a13d17a0cc7e0ceb0cc3555a66bb2d9a2d', $range[1]->getHash());
        self::assertSame('84c516b46713e139ae7c7560aa69f96ae3bb6700', $range[2]->getHash());
    }
}
