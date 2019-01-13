<?php

require_once(__DIR__.'/../../../php/utilities/do_stored_query.php');
require_once(__DIR__.'/DBWrapTestingDoStoredQuery.php');

final class do_stored_query_test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DBWrapTestingDoStoredQuery
     */
    private $db;

    protected function setUp()
    {
        $this->db = new DBWrapTestingDoStoredQuery();
        DBWrap::TEST_set_instance($this->db);
    }

    protected function tearDown()
    {
        DBWrap::TEST_set_instance(false);
    }

    /**
     * @dataProvider do_stored_query_data_provider
     * @param $arguments
     * @param $executedQuery
     */
    public function test_do_stored_query($arguments, $executedQuery)
    {
        do_stored_query($arguments);

        $this->assertSame($executedQuery, $this->db->executedQuery());
    }

    public function do_stored_query_data_provider()
    {
        return [
            [
                ['get_users'],
                'CALL get_users()'
            ],
            [
                [
                    'get_orders_listing',
                    '2018-11-03',
                    '9999-12-30',
                    1,
                    0,
                    10
                ],
                "CALL get_orders_listing('2018-11-03','9999-12-30','1','0','10')"
            ]
        ];
    }
}
