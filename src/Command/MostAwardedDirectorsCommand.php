<?php

namespace App\Command;

use App\Document\SampleMflix\Director;
use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function iterator_apply;
use function sprintf;
use function usort;

class MostAwardedDirectorsCommand extends Command
{
    public function __construct(
        private Collection $moviesCollection,
    ) {
        parent::__construct('mongodb:most-awarded-directors');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        try {
            $pipeline = [
                ['$sort' => ['awards.wins' => -1]],
                ['$addFields' => ['director' => '$directors']],
                ['$unwind' => '$director'],
                ['$addFields' => [
                    'winQuota' => ['$cond' => [
                        'if' => ['$gt' => ['$awards.nominations', 0]],
                        'then' => ['$divide' => ['$awards.wins', '$awards.nominations']],
                        'else' => 0,
                    ]],
                ]],
                ['$group' => [
                    '_id' => '$director',
                    'awardWins' => ['$sum' => '$awards.wins'],
                    'awardNominations' => ['$sum' => '$awards.nominations'],
                    'mostAwardedMovie' => ['$first' => '$$ROOT'],
// Use a reference with some denormalised data: not supported by the driver
//                    'movies' => ['$push' => [
//                        '_id' => '$_id',
//                        'title' => '$title',
//                        'awards' => '$awards',
//                    ]],
                ]],
                ['$replaceRoot' => ['newRoot' => [
                    'director' => '$_id',
                    'awards' => [
                        'wins' => '$awardWins',
                        'nominations' => '$awardNominations',
                        'text' => '',
                    ],
                    'mostAwardedMovie' => '$mostAwardedMovie',
                ]]],
// Requires allowDiskUse option which is not supported on shared Atlas tiers
//                ['$sort' => ['awards.wins' => -1]],
            ];

            /** @var Director[]|Cursor $directors */
            $directors = iterator_to_array($this->moviesCollection->aggregate(
                $pipeline,
                ['typeMap' => Director::getTypemap()],
            ));

            // Sort array manually since we can't sort in agg pipeline
            usort(
                $directors,
                fn (Director $a, Director $b) => $b->getAwards()->getWins() <=> $a->getAwards()->getWins(),
            );

            $table = new Table($output);
            $table->setHeaders(['Director', 'Awards Won', 'Most winning movie']);

            array_map(
                function (Director $director) use ($table): void {
                    $table->addRow([
                        $director->getDirector(),
                        $director->getAwards()->getWins(),
                        sprintf(
                            '%s (%d wins)',
                            $director->getMostAwardedMovie()->getTitle(),
                            $director->getMostAwardedMovie()->getAwards()->getWins(),
                        ),
                    ]);
                },
                array_slice($directors, 0, 10),
            );

            $table->render();
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>Could not run aggregation: %s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
