<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Entity\User;
use App\Tests\FixturesTrait;
use App\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Utils
 */
class UtilsTest extends WebTestCase
{
    use FixturesTrait;

    public function test toLength(): void
    {
        self::setUpClient(User::ROLE_USER);

        self::assertEquals(50, strlen(StringUtils::stringToLength('someting', 50)));
        self::assertEquals(15, strlen(StringUtils::stringToLength('someting', 15)));
        self::assertEquals(5, strlen(StringUtils::stringToLength('someting', 5)));
    }
}
