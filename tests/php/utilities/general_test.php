<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS);

require_once(__DIR__ . '/../../../php/utilities/general.php');

final class general_test extends \PHPUnit\Framework\TestCase
{
    public function testWorking()
    {
        $this->assertTrue(true);
    }
}
