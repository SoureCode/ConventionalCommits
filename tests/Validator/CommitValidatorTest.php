<?php

namespace SoureCode\ConventionalCommits\Tests\Validator;

use PHPUnit\Framework\TestCase;
use SoureCode\ConventionalCommits\Message\Message;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\Validator\Validator;
use Symfony\Component\Validator\Validation;

class CommitValidatorTest extends TestCase
{
    private static $defaultConfiguration = [
        'type' => [
            'min' => 2,
            'max' => 5,
            'extra' => false,
            'values' => ['foo', 'bar', 'baz'],
        ],
        'scope' => [
            'min' => 1,
            'max' => 2,
            'extra' => false,
            'required' => false,
            'values' => ['a', 'b', 'c', 'fi', 'fa', 'id'],
        ],
        'description' => [
            'min' => 3,
            'max' => 10,
        ],
    ];

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate(Message $message)
    {
        // Arrange
        $configurationLoader = $this->createMock(ConfigurationLoader::class);
        $validator = Validation::createValidator();
        $configurationLoader->method('load')->willReturn(self::$defaultConfiguration);
        $commitValidator = new Validator($validator, $configurationLoader);

        // Act
        $commitValidator->validate($message);

        // Do not throw!
        self::assertNull(null);
    }

    public function validateDataProvider(): array
    {
        $messages = [
            'foo(fi): baz',
            'bar(a): lorem ipsu',
            'baz: lorem ipsu',
        ];

        $items = array_map(
            static function (string $message) {
                return [Message::fromString($message)];
            },
            $messages
        );

        return $items;
    }

    /**
     * @dataProvider validateFailDataProvider
     */
    public function testValidateFail(Message $message, string $violation)
    {
        $this->expectExceptionMessageMatches($violation);

        // Arrange
        $configurationLoader = $this->createMock(ConfigurationLoader::class);
        $validator = Validation::createValidator();
        $configurationLoader->method('load')->willReturn(self::$defaultConfiguration);
        $commitValidator = new Validator($validator, $configurationLoader);

        // Act
        $commitValidator->validate($message);
    }

    public function validateFailDataProvider()
    {
        $messages = [
            ['ba: foo bar', '/header.type:\s+The value you selected is not a valid choice/'],
            ['bar(ok): foo bar', '/header.scope:\s+The value you selected is not a valid choice/'],
            ['bar(a): sh', '/header.description:\s+This value is too short/'],
            ['bar(a): lorem ipsum dolor', '/header.description:\s+This value is too long/'],
        ];

        $items = array_map(
            static function (array $item) {
                return [Message::fromString($item[0]), $item[1]];
            },
            $messages
        );

        return $items;
    }
}
