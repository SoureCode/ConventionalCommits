<?php

namespace SoureCode\ConventionalCommits\Commands;

use SoureCode\ConventionalCommits\Git\GitCommitRanges;
use SoureCode\ConventionalCommits\Message\Message;
use SoureCode\ConventionalCommits\Validator\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        $this->setDescription('Validate commit messages')
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

        foreach ($commits as $commit) {
            $message = Message::fromString(sprintf("%s\n%s", $commit->getSubject(), $commit->getBody()));

            $this->validator->validate($message);
        }

        $io->success('Message is valid.');

        return Command::SUCCESS;
    }
}
