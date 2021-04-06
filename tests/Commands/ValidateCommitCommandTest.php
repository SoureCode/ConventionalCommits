<?php

namespace SoureCode\ConventionalCommits\Tests\Commands;

use InvalidArgumentException;
use SoureCode\ConventionalCommits\Application;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidateCommitCommandTest extends KernelTestCase
{
    private static $defaultConfiguration = [
        'type' => [
            'min' => 1,
            'max' => 10,
            'extra' => false,
            'values' => ['feat', 'fix', 'test', 'chore', 'docs', 'refactor', 'revert'],
        ],
        'scope' => [
            'min' => 3,
            'max' => 10,
            'extra' => true,
            'required' => false,
            'values' => [],
        ],
        'description' => [
            'min' => 5,
            'max' => 50,
        ],
    ];

    /**
     * @dataProvider validateCommitCommandDataProvider
     */
    public function testValidateCommitCommand(string $commits): void
    {
        // Arrange
        $container = static::getContainer();
        $application = $container->get(Application::class);
        $command = $application->find('validate:commit');
        $commandTester = new CommandTester($command);

        // Act
        $exitCode = $commandTester->execute(
            [
                'commits' => $commits,
            ]
        );

        $output = $commandTester->getDisplay();

        self::assertStringContainsString('Message is valid.', $output, 'Expect commit message to be valid.');
        self::assertSame(0, $exitCode, 'Expect exit code to be 0.');
    }

    /**
     * @dataProvider validateCommitCommandInvalidDataProvider
     */
    public function testValidateCommitCommandInvalid(string $commits, string $exception, string $message): void
    {
        // Assert
        $this->expectExceptionMessage($message);
        $this->expectException($exception);

        // Arrange
        $container = static::getContainer();
        $application = $container->get(Application::class);
        $command = $application->find('validate:commit');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute(
            [
                'commits' => $commits,
            ]
        );
    }

    public function validateCommitCommandDataProvider(): array
    {
        return [
            // Single commit
            ['8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a'],
            ['8648bac1'],
            // Commit range
            ['41491a5c-7fd08bb1'],
            ['7fd08bb1-41491a5c'],
            // Multiple commit ranges
            ['584923ca-41491a5c,97b09e8b-7fd08bb1'],
            ['41491a5c-584923ca,97b09e8b-7fd08bb1'],
            ['584923ca-41491a5c,7fd08bb1-97b09e8b'],
            ['41491a5c-584923ca,7fd08bb1-97b09e8b'],
        ];
    }

    public function validateCommitCommandInvalidDataProvider(): array
    {
        return [
            ['3afed635', InvalidArgumentException::class, 'Invalid header format'],
            ['f2c00832', ValidationFailedException::class, 'This value is too long'],
            ['f2c00832', ValidationFailedException::class, 'The value you selected is not a valid choice'],
            ['7fd08bb1-2662bdb9', InvalidArgumentException::class, 'Invalid header format'],
            ['2662bdb9-7fd08bb1', InvalidArgumentException::class, 'Invalid header format'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $configurationLoader = $this->createMock(ConfigurationLoader::class);
        $configurationLoader->method('load')->willReturn(self::$defaultConfiguration);

        $container->set('app.configuration.loader', $configurationLoader);
    }
}
