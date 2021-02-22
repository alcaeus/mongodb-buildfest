<?php

namespace App\Command;

use MongoDB\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function iterator_apply;
use function sprintf;

class CustomerBirthdaysCommand extends Command
{
    public function __construct(
        private Collection $customersCollection,
    ) {
        parent::__construct('mongodb:customer-birthdays');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $agg = [
            [ '$group'=> [
                '_id'=> [ '$dateToString'=> [ 'format'=> '%m', 'date'=> '$birthdate' ] ],
                'birthdaysPerMonth'=> [ '$sum'=> 1 ],
                'customerList' => [ '$push' => [
                    'birthday' => '$birthdate',
                    'email' => '$email',
                    'name' =>'$name'
                ]]
            ]],
            [ '$sort'=> [ '_id'=> 1 ] ],
            [ '$project'=> [
                '_id' => 0,
                'birthdayMonth' => '$_id',
                'birthdaysPerMonth' => 1,
                'customerList'=> 1
            ]]
        ];

        try {
            $cursor = $this->customersCollection->aggregate($agg);

            $table = new Table($output);
            $table->setHeaders(['Month', 'Birthdays', 'Customers']);

            iterator_apply($cursor, function () use ($cursor, $table): bool {
                $row = $cursor->current();
                $table->addRow([$row->birthdayMonth, $row->birthdaysPerMonth, $row->customerList]);
                return true;
            });

            $table->render();

            $output->writeln('<info>Succeeded!</info>');
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>Could not run aggregation: %s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }
}
