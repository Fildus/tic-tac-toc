<?php

namespace App\Command;

use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseReadyCommand extends Command
{
    protected static $defaultName = 'app:database-ready';

    protected function configure()
    {
        $this->setDescription('checks if the test database is ready');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = 'root';
        $password = 'root';

        $dsn = 'mysql:host=db-test';
        $this->test($dsn, $username, $password);

        $output->writeln('database found');

        $dsn .= ';dbname=tic-tac-toc_test;';
        $this->test($dsn, $username, $password);

        $output->writeln('database ready');

        return 0;
    }

    private function test(string $dsn, string $username, string $password): bool
    {
        do {
            $db = null;
            try {
                $db = new PDO($dsn, $username, $password);
            } catch (\Exception $e) {
                sleep(0.1);
            }
        } while (!($db instanceof PDO));

        return true;
    }
}
