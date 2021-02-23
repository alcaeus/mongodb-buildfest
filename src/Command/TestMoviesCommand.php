<?php

namespace App\Command;

use App\Document\SampleMflix\Movie;
use MongoDB\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function sprintf;

class TestMoviesCommand extends Command
{
    public function __construct(
        private Collection $moviesCollection,
    ) {
        parent::__construct('mongodb:test-movies');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        try {
            /** @var Movie $movie */
            $typemap = Movie::getTypemap();
            $movie = $this->moviesCollection->findOne(
                [],
                ['typeMap' => $typemap]
            );

            $output->writeln(sprintf('<info>Succeeded: %s</info>', $movie->getTitle()));
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>Could not run aggregation: %s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }

    private function dump(mixed ...$args): string
    {
        ob_start();

        try {
            var_dump(...$args);
        } finally {
            return ob_get_clean();
        }
    }
}
