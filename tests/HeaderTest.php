<?php

namespace SoureCode\ConventionalCommits\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Header;

class HeaderTest extends TestCase
{
    public function testGetSetType(): void
    {
        // Arrange
        $header = Header::fromString('foo(bar)!: lorem ipsum');

        // Act and Assert
        self::assertSame('foo', $header->getType());
        self::assertSame('docs', $header->setType('docs')->getType());
        self::assertSame('foo', $header->getType());
    }

    public function testGetSetScope(): void
    {
        // Arrange
        $header = Header::fromString('foo(bar)!: lorem ipsum');

        // Act and Assert
        self::assertSame('bar', $header->getScope());
        self::assertSame('readme', $header->setScope('readme')->getScope());
        self::assertSame('bar', $header->getScope());
    }

    public function testGetSetIsBreakingChange(): void
    {
        // Arrange
        $header = Header::fromString('foo(bar)!: lorem ipsum');

        // Act and Assert
        self::assertTrue($header->isBreakingChange());
        self::assertFalse($header->setIsBreakingChange(false)->isBreakingChange());
        self::assertTrue($header->isBreakingChange());
    }

    public function testGetSetDescription(): void
    {
        // Arrange
        $header = Header::fromString('foo(bar)!: lorem ipsum');

        // Act and Assert
        self::assertSame('lorem ipsum', $header->getDescription());
        self::assertSame('fix collapsable menu', $header->setDescription('fix collapsable menu')->getDescription());
        self::assertSame('lorem ipsum', $header->getDescription());
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $input, string $type, ?string $scope, bool $isBreakingChange, string $description): void
    {
        // Arrange and Act
        $header = Header::fromString($input);

        // Assert
        self::assertSame($type, $header->getType());
        self::assertSame($scope, $header->getScope());
        self::assertSame($isBreakingChange, $header->isBreakingChange());
        self::assertSame($description, $header->getDescription());
    }

    /**
     * @dataProvider fromStringInvalidDataProvider
     */
    public function testInvalidHeader(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange and Act
        Header::fromString($input);
    }

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(string $input): void
    {
        // Arrange
        $header = Header::fromString($input);

        // Act and Assert
        self::assertSame($input, $header->toString());
    }

    public function fromStringDataProvider(): array
    {
        return [
            ['foo(bar)!: lorem ipsum', 'foo', 'bar', true, 'lorem ipsum'],
            ['bar(foo)!: dolor amet', 'bar', 'foo', true, 'dolor amet'],
            ['docs(readme): update date', 'docs', 'readme', false, 'update date'],
            ['docs(api)!: add new endpoint', 'docs', 'api', true, 'add new endpoint'],
            ['foo!: lorem ipsum', 'foo', null, true, 'lorem ipsum'],
            ['bar!: dolor amet', 'bar', null, true, 'dolor amet'],
            ['docs: update date', 'docs', null, false, 'update date'],
            ['docs!: add new endpoint', 'docs', null, true, 'add new endpoint'],
        ];
    }

    public function fromStringInvalidDataProvider(): array
    {
        return [
            [''],
            ['foo(bar!: bar bats'],
            ['foobar)!: bar bats'],
            ['foo(bar)! bar bats'],
            ['foo(bar)!:bar bats'],
            ['foo bat(bar)!: bar bats'],
            ['foobar(bar)!: '],
            ['foo bat(bar): foo'],
            ["foo-bat(bar): foo\nbar"],
        ];
    }

    public function toStringDataProvider(): array
    {
        return [
            ['foo(bar)!: lorem ipsum'],
            ['bar(foo)!: dolor amet'],
            ['docs(readme): update date'],
            ['docs(api)!: add new endpoint'],
            ['foo!: lorem ipsum'],
            ['bar!: dolor amet'],
            ['docs: update date'],
            ['docs!: add new endpoint'],
        ];
    }
}
