<?php

require_once(__DIR__.'/../../../php/utilities/useruf.php');

final class useruf_test extends \PHPUnit\Framework\TestCase
{
    const A_FAMILY_UNIT_ID = 1;
    const USERNAME_IN_USER = 'admin';

    /** @test */
    public function should_create_a_member_from_request_data()
    {
        $_REQUEST['name'] = 'name';

        create_user_member(self::A_FAMILY_UNIT_ID);

        // TODO: Break dependencies or create seams for true assertion
        $this->assertTrue(true);
    }

    /** @test */
    public function should_throw_an_exception_when_username_is_in_use()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage(sprintf("The login '%s' already exists. Please choose another one", self::USERNAME_IN_USER));

        $_REQUEST['name'] = 'name';
        $_REQUEST['login'] = self::USERNAME_IN_USER;
        create_user_member(self::A_FAMILY_UNIT_ID);
    }
}
