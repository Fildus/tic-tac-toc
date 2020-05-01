<?php

use App\Tests\Database;

require dirname(__DIR__).'/vendor/autoload.php';

require dirname(__DIR__).'/config/bootstrap.php';

Database::reload();
