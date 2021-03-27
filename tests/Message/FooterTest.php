<?php

namespace SoureCode\ConventionalCommits\Tests\Message;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Message\Footer;

class FooterTest extends TestCase
{
    public function testGetSetValue(): void
    {
        // Arrange
        $footer = Footer::fromString("bar-foo: lorem ipsum\nbar foobar");

        // Act and Assert
        self::assertSame("lorem ipsum\nbar foobar", $footer->getValue());
        self::assertSame('lorem', $footer->setValue('lorem')->getValue());
        self::assertSame("lorem ipsum\nbar foobar", $footer->getValue());
    }

    public function testGetSeparatorType(): void
    {
        // Arrange
        $footer = Footer::fromString("bar-foo: lorem ipsum\nbar foobar");

        // Act and Assert
        self::assertSame(Footer::SEPARATOR_TYPE_COLON, $footer->getSeparatorType());
        self::assertSame(Footer::SEPARATOR_TYPE_HASH, $footer->setSeparatorType(Footer::SEPARATOR_TYPE_HASH)->getSeparatorType());
        self::assertSame(Footer::SEPARATOR_TYPE_COLON, $footer->getSeparatorType());
    }

    public function testGetKey(): void
    {
        // Arrange
        $footer = Footer::fromString("bar-foo: lorem ipsum\nbar foobar");

        // Act and Assert
        self::assertSame('bar-foo', $footer->getKey());
        self::assertSame('foo-bar', $footer->setKey('foo-bar')->getKey());
        self::assertSame('bar-foo', $footer->getKey());
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $input, string $key, string $separatorType, string $value): void
    {
        // Arrange and Act
        $footer = Footer::fromString($input);

        // Assert
        self::assertSame($key, $footer->getKey());
        self::assertSame($separatorType, $footer->getSeparatorType());
        self::assertSame($value, $footer->getValue());
    }

    /**
     * @dataProvider fromStringInvalidDataProvider
     */
    public function testInvalidFromString(string $input): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange and Act
        Footer::fromString($input);
    }

    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(string $input)
    {
        // Arrange
        $footer = Footer::fromString($input);

        // Act and Assert
        self::assertSame($input, (string) $footer);
    }

    public function fromStringDataProvider()
    {
        return [
            ['foo-bar: foo', 'foo-bar', Footer::SEPARATOR_TYPE_COLON, 'foo'],
            ['foo: batz', 'foo', Footer::SEPARATOR_TYPE_COLON, 'batz'],
            ['BREAKING CHANGE: foobar', 'BREAKING CHANGE', Footer::SEPARATOR_TYPE_COLON, 'foobar'],
            ["foo-bar: foo\nbar", 'foo-bar', Footer::SEPARATOR_TYPE_COLON, "foo\nbar"],
            ["foo: batz\nbar", 'foo', Footer::SEPARATOR_TYPE_COLON, "batz\nbar"],
            ["BREAKING CHANGE: foobar\nbar", 'BREAKING CHANGE', Footer::SEPARATOR_TYPE_COLON, "foobar\nbar"],
            ['foo-bar #foo', 'foo-bar', Footer::SEPARATOR_TYPE_HASH, 'foo'],
            ['foo #batz', 'foo', Footer::SEPARATOR_TYPE_HASH, 'batz'],
            ['BREAKING CHANGE #foobar', 'BREAKING CHANGE', Footer::SEPARATOR_TYPE_HASH, 'foobar'],
            ["foo-bar #foo\nbar", 'foo-bar', Footer::SEPARATOR_TYPE_HASH, "foo\nbar"],
            ["foo #batz\nbar", 'foo', Footer::SEPARATOR_TYPE_HASH, "batz\nbar"],
            ["BREAKING CHANGE #foobar\nbar", 'BREAKING CHANGE', Footer::SEPARATOR_TYPE_HASH, "foobar\nbar"],
        ];
    }

    public function fromStringInvalidDataProvider(): array
    {
        return [
            [''],
            ['foo bar: batz'],
            ['foo-bar:batz'],
            ['foo-bar batz'],
            ['foo-bar:'],
            ['foo-bar: '],
            ['foo-bar #'],
            ['foo-bar#'],
        ];
    }

    public function toStringDataProvider(): array
    {
        return [
            ['foo-bar: foo'],
            ['foo: batz', 'foo'],
            ['BREAKING CHANGE: foobar'],
            ["foo-bar: foo\nbar"],
            ["foo: batz\nbar"],
            ["BREAKING CHANGE: foobar\nbar"],
            ['foo-bar #foo'],
            ['foo #batz'],
            ['BREAKING CHANGE #foobar'],
            ["foo-bar #foo\nbar"],
            ["foo #batz\nbar"],
            ["BREAKING CHANGE #foobar\nbar"],
        ];
    }
}
