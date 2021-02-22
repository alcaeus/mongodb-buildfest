<?php

namespace App\Command;

use MongoDB\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function sprintf;

class TestMongoDBConnectionCommand extends Command
{
    public function __construct(
        private Database $database,
    ) {
        parent::__construct('mongodb:test-connection');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->database->command(['ping' => 1]);
            $output->writeln('<info>Succeeded!</info>');
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>Could not run ping: %s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
