<?php

namespace SoureCode\ConventionalCommits\Tests\Commands;

use SoureCode\ConventionalCommits\Application;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\GitWrapper\GitCommit;
use Symplify\GitWrapper\GitCommits;
use Symplify\GitWrapper\GitWorkingCopy;
use Symplify\GitWrapper\GitWrapper;

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

    private static $commitMessages = [
        // Resolve long hashes
        '8648bac1' => [
            'hash' => '8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a',
        ],
        '3afed635' => [
            'hash' => '3afed635063877a09a1218f8745a52c802cea7c1',
        ],
        'f2c00832' => [
            'hash' => 'f2c00832f3b157e4c97d9cc303fd9ff45b3d859f',
        ],
        // Resolve message
        '8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a' => [
            'subject' => 'refactor(commit): Move classes into another namespace',
            'body' => <<<HEREDOC
- foo bar

foo: bar
HEREDOC,
        ],
        '3afed635063877a09a1218f8745a52c802cea7c1' => [
            'subject' => 'Add initial set of files',
        ],
        '7fd08bb195ac66087a444d399a4882dce653a337' => [
            'subject' => 'chore: Fix dependency and some psalm errors',
        ],
        '97b09e8bd07cc1f9997ac645c314f4ec3c55deb6' => [
            'subject' => 'fix(chore): Fix CS and psalm types',
        ],
        '41491a5c3edc28a4d1ecd4011987445adbea5239' => [
            'subject' => 'fix(tests): Fix validate command test',
        ],
        '584923ca57a0f0dfaf355eadbb8afe5ff0688d4a' => [
            'subject' => 'fix(git): Fix return value',
        ],
        'f2c00832f3b157e4c97d9cc303fd9ff45b3d859f' => [
            'subject' => 'somethingisreallywrongwith: this commit',
        ],
        '2662bdb90202d93cd19c264960be45099b037d7a' => [
            'subject' => 'Add validate command',
            'body' => <<<HEREFOC
- Add configuration loader
- Add validation
- Add custom kernel
- Add application
HEREFOC,
        ],
    ];

    /**
     * @dataProvider validateCommitCommandDataProvider
     *
     * @param Array<string, string[]> $rangeMapping
     */
    public function testValidateCommitCommand(array $rangeMapping): void
    {
        // Arrange
        /**
         * @var string $commits
         */
        $commits = array_key_first($rangeMapping);
        $commitsMapping = $rangeMapping[$commits];
        $container = static::getContainer();
        $this->mockGitWrapper($container, $commitsMapping);

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

        self::assertStringContainsString('Messages are valid.', $output, 'Expect commit message to be valid.');
        self::assertSame(0, $exitCode, 'Expect exit code to be 0.');
    }

    private function mockGitWrapper(ContainerInterface $container, array $commitsMapping)
    {
        $wrapperMock = $this->createMock(GitWrapper::class);
        $workingCopyMock = $this->createMock(GitWorkingCopy::class);
        $commitsMock = $this->createMock(GitCommits::class);

        $wrapperMock->method('workingCopy')->willReturn($workingCopyMock);
        $workingCopyMock->method('commits')->willReturn($commitsMock);
        $commitsMock->method('fetchRange')->willReturnCallback(
            function () use ($commitsMapping) {
                return $commitsMapping;
            }
        );

        $now = (new \DateTime())->format(DATE_ATOM);
        $commitsMock->method('get')->willReturnCallback(
            function (string $commitString) use ($now): GitCommit {
                $message = static::$commitMessages[$commitString];
                $commit = new GitCommit(
                    array_merge(
                        [
                            'body' => '',
                            'subject' => '',
                            'hash' => $commitString,
                            'author' => 'none',
                            'authorDate' => $now,
                            'committer' => 'none',
                            'committerDate' => $now,
                        ],
                        $message,
                    )
                );

                return $commit;
            }
        );

        $container->set(GitWrapper::class, $wrapperMock);
    }

    /**
     * @dataProvider validateCommitCommandInvalidDataProvider
     *
     * @param Array<string, string[]> $rangeMapping
     */
    public function testValidateCommitCommandInvalid(array $rangeMapping, string $message): void
    {
        // Arrange
        $commits = array_key_first($rangeMapping);
        $commitsMapping = $rangeMapping[$commits];
        $container = static::getContainer();
        $this->mockGitWrapper($container, $commitsMapping);
        $application = $container->get(Application::class);
        $command = $application->find('validate:commit');
        $commandTester = new CommandTester($command);

        // Act
        $exitCode = $commandTester->execute(
            [
                'commits' => $commits,
            ]
        );

        // Assert
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($message, $output, 'Expect commit message to be valid.');
        self::assertSame(1, $exitCode, 'Expect exit code to be 1.');
    }

    public function validateCommitCommandDataProvider(): array
    {
        $range1 = [
            '7fd08bb195ac66087a444d399a4882dce653a337',
            '97b09e8bd07cc1f9997ac645c314f4ec3c55deb6',
            '41491a5c3edc28a4d1ecd4011987445adbea5239',
        ];

        $range2 = [
            '41491a5c3edc28a4d1ecd4011987445adbea5239',
            '584923ca57a0f0dfaf355eadbb8afe5ff0688d4a',
            '7fd08bb195ac66087a444d399a4882dce653a337',
            '97b09e8bd07cc1f9997ac645c314f4ec3c55deb6',
        ];

        return [
            // Single commits
            [
                ['8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a' => ['8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a']],
            ],
            [
                ['8648bac1' => ['8648bac12b9c363f4a7e30cfc95a20b2fcc3f46a']],
            ],
            // Commit ranges
            [
                [
                    '41491a5c...7fd08bb1' => $range1,
                ],
            ],
            [
                [
                    '7fd08bb1...41491a5c' => $range1,
                ],
            ],
            // Multiple commit ranges
            [
                [
                    '584923ca...41491a5c,97b09e8b...7fd08bb1' => $range2,
                ],
            ],
            [
                [
                    '41491a5c...584923ca,97b09e8b...7fd08bb1' => $range2,
                ],
            ],
            [
                [
                    '584923ca...41491a5c,7fd08bb1...97b09e8b' => $range2,
                ],
            ],
            [
                [
                    '41491a5c...584923ca,7fd08bb1...97b09e8b' => $range2,
                ],
            ],
        ];
    }

    public function validateCommitCommandInvalidDataProvider(): array
    {
        return [
            [
                ['3afed635' => ['3afed635063877a09a1218f8745a52c802cea7c1']],
                'Invalid header format',
            ],
            [
                ['f2c00832' => ['f2c00832f3b157e4c97d9cc303fd9ff45b3d859f']],
                'This value is too long',
            ],
            [
                ['f2c00832' => ['f2c00832f3b157e4c97d9cc303fd9ff45b3d859f']],
                'The value you selected is not a valid choice',
            ],
            [
                [
                    '7fd08bb1...2662bdb9' => [
                        '2662bdb90202d93cd19c264960be45099b037d7a',
                        '7fd08bb195ac66087a444d399a4882dce653a337',
                    ],
                ],
                'Invalid header format',
            ],
            [
                [
                    '2662bdb9...7fd08bb1' => [
                        '2662bdb90202d93cd19c264960be45099b037d7a',
                        '7fd08bb195ac66087a444d399a4882dce653a337',
                    ],
                ],
                'Invalid header format',
            ],
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
