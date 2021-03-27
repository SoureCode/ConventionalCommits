<?php

namespace SoureCode\ConventionalCommits\Commands;

use SoureCode\ConventionalCommits\Message\Message;
use SoureCode\ConventionalCommits\Validator\Validator;
use const STDIN;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidateCommand extends Command
{
    protected static $defaultName = 'validate';

    protected Validator $validator;

    public function __construct(Validator $validator)
    {
        parent::__construct();

        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this->setDescription('Validate commit message')
            ->addArgument('message', InputArgument::REQUIRED, 'The commit message')
            ->setHelp(<<<HELP
Examples:
    Use it as argument:
    conventional-commits validate "feat(api): Add id param converter"

    Or just pipe it in:
    git log --pretty=format:'%B' -n 1 | conventional-commits validate
HELP
);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $message = $input->getArgument('message');

        if (!$message) {
            $inputSteam = ($input instanceof StreamableInputInterface) ? $input->getStream() : null;
            $inputSteam = $inputSteam ?? STDIN;

            $message = stream_get_contents($inputSteam);

            $input->setArgument('message', $message);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /**
         * @var string $inputMessage
         */
        $inputMessage = $input->getArgument('message');

        try {
            $message = Message::fromString($inputMessage);

            $this->validator->validate($message);
        } catch (ValidationFailedException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success('Message is valid.');

        return Command::SUCCESS;
    }
}
