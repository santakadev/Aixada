<?php

require_once(__DIR__.'/../../../php/utilities/useruf.php');

global $create_user_from_values_should_return;

function extract_user_form_values(){
    global $create_user_from_values_should_return;
    return $create_user_from_values_should_return;
}

final class useruf_test extends \PHPUnit\Framework\TestCase
{
    const A_FAMILY_UNIT_ID = 1;
    const USERNAME_IN_USE = 'admin';

    /** @test */
    public function should_create_a_member_from_request_data()
    {
        global $create_user_from_values_should_return;
        $create_user_from_values_should_return = [
            'name' => 'name',
            'login' => 'login',
            'password' => 'password',
            'custom_member_ref' => 'custom_member_ref',
            'nif' => 'nif',
            'address' => 'address',
            'city' => 'city',
            'zip' => 'zip',
            'phone1' => 'phone1',
            'phone2' => 'phone2',
            'web' => 'web',
            'notes' => 'notes',
            'active' => true,
            'participant' => true,
            'adult' => true,
            'language' => 'es',
            'gui_theme' => 'gui_theme',
            'email' => 'email'
        ];

        create_user_member(self::A_FAMILY_UNIT_ID);

        $this->assertTrue(true);
    }

    /** @test */
    public function should_throw_an_exception_when_username_is_in_use()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage(sprintf("The login '%s' already exists. Please choose another one", self::USERNAME_IN_USE));

        global $create_user_from_values_should_return;
        $create_user_from_values_should_return = [
            'name' => 'name',
            'login' => self::USERNAME_IN_USE
        ];

        create_user_member(self::A_FAMILY_UNIT_ID);
    }
}
