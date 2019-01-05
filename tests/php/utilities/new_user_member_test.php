<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS);

require_once(__DIR__.'/../../../php/inc/database.php');
require_once(__DIR__.'/../../local_config/configuration_vars_test.php');

final class new_user_member_test extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        configuration_vars::TEST_set_test_instance(new configuration_vars_test());
    }

    protected function tearDown()
    {
        do_stored_query('del_user_member', 2);
    }

    public function test_new_user_member()
    {
        $result = DBWrap::get_instance()->Select('max(id) AS max_id', 'aixada_user', '', '');
        $maxId = $result->fetch_object()->max_id;

        do_stored_query(
            'new_user_member',
            'login',            // login        string(50)
            'password',         // password     string(255)
            1,                  // uf_id        int,
            'custom_ref',       // customer_ref string(100),
            'name',             // name         string(255),
            'nif',              // nif          string(15),
            'address',          // address      string(255),
            'city',             // city         string(255),
            'zip',              // zip          string(10),
            'phone1',           // phone1       string(50),
            'phone2',           // phone2       string(50),
            'web',              // web          string(255),
            'notes',            // notes        string(text),
            1,                  // active       bool,
            1,                  // participant  bool,
            1,                  // adult        bool,
            'es',               // language     char(5),
            'gui_theme',        // gui_theme    string(50),
            'email'             // email        string(100),
        );

        // Assert aixada_member
        $result = DBWrap::get_instance()->Select('*', 'aixada_member', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newMemberRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertEquals($maxId + 1, $newMemberRow['id']);
        $this->assertSame('custom_ref', $newMemberRow['custom_member_ref']);
        $this->assertSame('1', $newMemberRow['uf_id']);
        $this->assertSame('name', $newMemberRow['name']);
        $this->assertSame('address', $newMemberRow['address']);
        $this->assertSame('nif', $newMemberRow['nif']);
        $this->assertSame('zip', $newMemberRow['zip']);
        $this->assertSame('city', $newMemberRow['city']);
        $this->assertSame('phone1', $newMemberRow['phone1']);
        $this->assertSame('phone2', $newMemberRow['phone2']);
        $this->assertSame('web', $newMemberRow['web']);
        $this->assertNull($newMemberRow['bank_name']);
        $this->assertNull($newMemberRow['bank_account']);
        $this->assertNull($newMemberRow['picture']);
        $this->assertSame('notes', $newMemberRow['notes']);
        $this->assertSame('1', $newMemberRow['active']);
        $this->assertSame('1', $newMemberRow['participant']);
        $this->assertSame('1', $newMemberRow['adult']);
        $this->assertLessThanOrEqual((int)date('U'), (new \DateTimeImmutable($newMemberRow['ts']))->getTimestamp());

        // Assert aixada_user
        $result = DBWrap::get_instance()->Select('*', 'aixada_user', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newUserRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertSame('2', $newUserRow['id']);
        $this->assertSame('login', $newUserRow['login']);
        $this->assertSame('password', $newUserRow['password']);
        $this->assertSame('1', $newUserRow['uf_id']);
        $this->assertSame('2', $newUserRow['member_id']);
        $this->assertNull($newUserRow['provider_id']);
        $this->assertSame('es', $newUserRow['language']);
        $this->assertSame('gui_theme', $newUserRow['gui_theme']);
        $this->assertNull($newUserRow['last_login_attempt']);
        $this->assertNull($newUserRow['last_successful_login']);
        $this->assertLessThanOrEqual((int)date('U'), (new \DateTimeImmutable($newUserRow['created_on']))->getTimestamp());

        // Assert aixada_user_role
        $result = DBWrap::get_instance()->Select('*', 'aixada_user_role', 'user_id = 2', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertCount(2, $rows);
        $this->assertSame('Checkout', $rows[0]['role']);
        $this->assertSame('Consumer', $rows[1]['role']);
    }
}