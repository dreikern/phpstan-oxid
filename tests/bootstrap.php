<?php declare(strict_types=1);

use PHPStan\Testing\PHPStanTestCase;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../source/oxfunctions.php';

function startProfile($s)
{
}
function stopProfile($s)
{
}

PHPStanTestCase::getContainer();
