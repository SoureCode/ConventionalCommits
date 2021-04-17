<?php

namespace SoureCode\ConventionalCommits\Commands;

use SoureCode\ConventionalCommits\Git\GitCommitRanges;
use SoureCode\ConventionalCommits\Message\Message;
use SoureCode\ConventionalCommits\Validator\Validator;
use function strlen;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ValidateCommitCommand extends Command
{
    protected static $defaultName = 'validate:commit';

    private Validator $validator;

    private GitCommitRanges $commitIdentifierParser;

    public function __construct(GitCommitRanges $commitIdentifierParser, Validator $validator)
    {
        parent::__construct();

        $this->commitIdentifierParser = $commitIdentifierParser;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this->setDescription('Validate commits')
            ->addArgument('commits', InputArgument::REQUIRED, 'The commit hash, hashes, range or ranges.')
            ->addUsage('8648bac1')
            ->addUsage('8648bac1-8d0536a1,2662bdb9')
            ->addUsage('3afed635-8648bac1,84c516b4-2662bdb9');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /**
         * @var string $inputCommits
         */
        $inputCommits = $input->getArgument('commits');

        $commits = $this->commitIdentifierParser->fetchRanges($inputCommits);

        $exitCode = Command::SUCCESS;

        foreach ($commits as $commit) {
            if ($io->isVeryVerbose()) {
                $io->writeln(sprintf('Commit: %s', $commit->getHash()));
                $io->writeln(sprintf('Subject: %s', $commit->getSubject()));
                $body = $commit->getBody();

                if (strlen($body) > 0) {
                    $io->writeln(sprintf('Body: %s', $commit->getBody()));
                }

                $io->write('Validate ...', false);
            }

            try {
                $message = Message::fromString(sprintf("%s\n%s", $commit->getSubject(), $commit->getBody()));

                $this->validator->validate($message);

                if ($io->isVeryVerbose()) {
                    $io->write(' valid!', true);
                }
            } catch (Throwable $exception) {
                if ($io->isVeryVerbose()) {
                    $io->write(' invalid!', true);
                }

                $io->error($exception->getMessage());

                $exitCode = Command::FAILURE;
            }
        }

        if (Command::SUCCESS === $exitCode) {
            $io->success('Messages are valid.');
        }

        return $exitCode;
    }
}
