<?php

namespace SoureCode\ConventionalCommits\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Footer;
use SoureCode\ConventionalCommits\Header;
use SoureCode\ConventionalCommits\Message;

class MessageTest extends TestCase
{
    public function testGetSetHeader(): void
    {
        // Arrange
        $header = Header::fromString('foo(bar)!: lorem ipsum');
        $message = Message::fromString("docs(readme)!: destroy the stuff\r\n\r\nlorem ipsum\r\n\r\ndolor amet\r\n\r\nfoo: bar\nBREAKING CHANGE: foo\nfix #1342");

        // Act and Assert
        self::assertSame('docs(readme)!: destroy the stuff', $message->getHeader()->toString());
        self::assertSame('foo(bar)!: lorem ipsum', $message->setHeader($header)->getHeader()->toString());
        self::assertSame('docs(readme)!: destroy the stuff', $message->getHeader()->toString());
    }

    public function testGetSetBody(): void
    {
        // Arrange
        $message = Message::fromString("docs(readme)!: destroy the stuff\r\n\r\nlorem ipsum\r\n\r\ndolor amet\r\n\r\nfoo: bar\nBREAKING CHANGE: foo\nfix #1342");

        // Act and Assert
        self::assertSame("lorem ipsum\n\ndolor amet", $message->getBody());
        self::assertSame("foo\n\nbar", $message->setBody("foo\n\nbar")->getBody());
        self::assertSame("lorem ipsum\n\ndolor amet", $message->getBody());
    }

    public function testGetSetFooters(): void
    {
        // Arrange
        $footers = [
            Footer::fromString("bar-foo: lorem ipsum\nbar foobar"),
            Footer::fromString("foo-bar: dolor\namet"),
        ];

        $message = Message::fromString("docs(readme)!: destroy the stuff\r\n\r\nlorem ipsum\r\n\r\ndolor amet\r\n\r\nfoo: bar\nBREAKING CHANGE: foo\nfix #1342");

        // Act and Assert
        self::assertSame("foo: bar\nBREAKING CHANGE: foo\nfix #1342", $this->stringifyFooters($message->getFooters()));
        self::assertSame("bar-foo: lorem ipsum\nbar foobar\nfoo-bar: dolor\namet", $this->stringifyFooters($message->setFooters($footers)->getFooters()));
        self::assertSame("foo: bar\nBREAKING CHANGE: foo\nfix #1342", $this->stringifyFooters($message->getFooters()));
    }

    /**
     * @param Footer[] $footers
     */
    private function stringifyFooters(array $footers): string
    {
        $stringParts = [];

        foreach ($footers as $footer) {
            $stringParts[] = $footer->toString();
        }

        return implode("\n", $stringParts);
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $input, string $header, ?string $body, string $footer): void
    {
        // Arrange and Act
        $message = Message::fromString($input);

        // Assert
        self::assertSame($header, $message->getHeader()->toString());
        self::assertSame($body, $message->getBody());
        self::assertSame($footer, $this->stringifyFooters($message->getFooters()));
    }

    /**
     * @dataProvider fromStringInvalidDataProvider
     */
    public function testInvalidFromString(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange and Act
        Message::fromString($input);
    }

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(string $input): void
    {
        // Arrange
        $message = Message::fromString($input);

        // Act and Assert
        self::assertSame($input, $message->toString());
    }

    public function fromStringDataProvider()
    {
        return [
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum\n\nfoo: bar\nbar #123", 'foo: bar', "foo\nbar\n\nlorem\n\ipsum", "foo: bar\nbar #123"],
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum\n\nfoo: bar\nlorem\nipsum\nbar #123", 'foo: bar', "foo\nbar\n\nlorem\n\ipsum", "foo: bar\nlorem\nipsum\nbar #123"],
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum", 'foo: bar', "foo\nbar\n\nlorem\n\ipsum", ''],
            ["foo: bar\n\nfoo: bar\nbar #123", 'foo: bar', null, "foo: bar\nbar #123"],
            ['foo: bar', 'foo: bar', null, ''],
        ];
    }

    public function toStringDataProvider()
    {
        return [
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum\n\nfoo: bar\nbar #123"],
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum\n\nfoo: bar\nlorem\nipsum\nbar #123"],
            ["foo: bar\n\nfoo\nbar\n\nlorem\n\ipsum"],
            ["foo: bar\n\nfoo: bar\nbar #123"],
            ['foo: bar'],
        ];
    }

    public function fromStringInvalidDataProvider(): array
    {
        return [
            [''],
        ];
    }
}
