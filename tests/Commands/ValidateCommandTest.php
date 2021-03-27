<?php

namespace SoureCode\ConventionalCommits\Tests\Commands;

use SoureCode\ConventionalCommits\Application;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\Validation;

class ValidateCommandTest extends KernelTestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $configurationLoader = $this->createMock(ConfigurationLoader::class);
        $configurationLoader->method('load')->willReturn(self::$defaultConfiguration);

        $container->set('app.configuration.loader', $configurationLoader);
    }


    /**
     * @dataProvider validateCommandDataProvider
     */
    public function testValidateCommand(string $input)
    {
        // Arrange
        $container = static::getContainer();
        $application = $container->get(Application::class);
        $command = $application->find('validate');
        $commandTester = new CommandTester($command);

        // Act
        $exitCode = $commandTester->execute(
            [
                'message' => $input,
            ]
        );

        $output = $commandTester->getDisplay();

        self::assertStringContainsString('Message message is valid.', $output, 'Expect commit message to be valid.');
        self::assertSame(0, $exitCode, 'Expect exit code to be 0.');
    }

    /**
     * @dataProvider validateCommandInvalidDataProvider
     */
    public function testValidateCommandInvalid(string $input, string $message)
    {
        // Arrange
        $container = static::getContainer();
        $application = $container->get(Application::class);
        $command = $application->find('validate');
        $commandTester = new CommandTester($command);

        // Act
        $exitCode = $commandTester->execute(
            [
                'message' => $input,
            ]
        );

        $output = $commandTester->getDisplay();

        self::assertStringContainsString('ERROR', $output, $message);
        self::assertSame(1, $exitCode, 'Expect exit code to be 1.');
    }

    public function validateCommandDataProvider()
    {
        $variations = [
            ['feat: Add new feature'],
            ['fix: Fix timespan'],
            ['docs: Update date in readme'],
            ['refactor: Rename date to day'],
            ['revert: 8648bac1 Move classes into another namespace'],
            // With scope
            ['feat(layout): Add new feature'],
            ['fix(date): Fix timespan'],
            ['docs(readme): Update date in readme'],
            ['refactor(date): Rename date to day'],
            ['revert(api): 8648bac1 Move classes into another namespace'],
            // With breaking change
            ['feat!: Add new feature'],
            ['fix!: Fix timespan'],
            ['docs!: Update date in readme'],
            ['refactor!: Rename date to day'],
            ['revert!: 8648bac1 Move classes into another namespace'],
            // With scope an breaking change
            ['feat(layout)!: Add new feature'],
            ['fix(date)!: Fix timespan'],
            ['docs(readme)!: Update date in readme'],
            ['refactor(date)!: Rename date to day'],
            ['revert(api)!: 8648bac1 Move classes into another namespace'],
        ];

        $items = [];

        foreach ($variations as [$value]) {
            $items[] = [$value];
            $items[] = [sprintf("%s\n\nLorem ipsum dolor amet\nLorem ipsum dolor amet", $value)];
            $items[] = [sprintf("%s\n\nfoo: bar", $value)];
            $items[] = [sprintf("%s\n\nfoo-bar: bar", $value)];
            $items[] = [sprintf("%s\n\nBREAKING CHANGE: foo", $value)];
            $items[] = [sprintf("%s\n\nLorem ipsum dolor amet\nLorem ipsum dolor amet\n\nfoo: bar", $value)];
            $items[] = [sprintf("%s\n\nLorem ipsum dolor amet\nLorem ipsum dolor amet\n\nfoo-bar: bar", $value)];
            $items[] = [sprintf("%s\n\nLorem ipsum dolor amet\nLorem ipsum dolor amet\n\nBREAKING CHANGE: foo", $value)];
        }

        return $items;
    }

    public function validateCommandInvalidDataProvider(): array
    {
        return [
            ['lorem: ipsum', 'Invalid type'],
            ['feat: lorem ipsum dolor amet lorem ipsum dolor amet lorem ipsum dolor amet', 'To long description'],
            ['feat: lor', 'To short description'],
            ['feat(a): lorem', 'To short scope'],
            ['feat(loremipsumdoloramet): lorem', 'To long scope'],
        ];
    }
}
