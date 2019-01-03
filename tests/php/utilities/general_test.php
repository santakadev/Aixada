<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS);

require_once(__DIR__.'/../../../php/utilities/general.php');
require_once(__DIR__.'/DBWrapTestingDoStoredQuery.php');

final class general_test extends \PHPUnit\Framework\TestCase
{
    public function test_do_stored_query()
    {
        $testingDb = new DBWrapTestingDoStoredQuery();

        DBWrap::TEST_set_instance($testingDb);
        do_stored_query('get_users');

        $this->assertSame('CALL get_users()', $testingDb->executedQuery());
    }
}
