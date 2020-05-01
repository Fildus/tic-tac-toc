<?php

namespace App\Tests;

use App\Kernel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

class Database
{
    public static function reload(): void
    {
        $kernel = new Kernel('test', true);
        $kernel->boot();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        try {
            $application->run(new StringInput('doctrine:database:drop --if-exists --force -q'));
            $application->run(new StringInput('doctrine:database:create --env=test --if-not-exists'));
            $application->run(new StringInput('doctrine:schema:update -env=test --force'));
            $application->run(new StringInput('doctrine:fixtures:load -env=test --group=test -n'));
        } catch (Exception $e) {
            new Exception($e->getMessage());
        }

        $kernel->shutdown();
    }
}
