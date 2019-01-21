<?php

require_once(__DIR__.'/../../../php/utilities/useruf.php');
require_once(__DIR__.'/new_user_member_test.php');
require_once(__DIR__.'/../../../php/inc/database.php');
require_once(__DIR__.'/../../../php/inc/adminDatabase.php');
require_once(__DIR__.'/../../local_config/configuration_vars_test.php');

global $create_user_from_values_should_return;

function extract_user_form_values(){
    global $create_user_from_values_should_return;
    return $create_user_from_values_should_return;
}

final class useruf_test extends \PHPUnit\Framework\TestCase
{
    const A_FAMILY_UNIT_ID = 1;
    const USERNAME_IN_USE = 'admin';

    /**
     * @var mysqli
     */
    private $db;

    protected function setUp()
    {
        configuration_vars::TEST_set_test_instance(new configuration_vars_test());
        $this->initializeDefaultDatabase();
    }

    protected function tearDown()
    {
        $this->db->close();
    }

    /** @test */
    public function should_create_a_member_from_request_data()
    {
        $newUserMemberArguments = NewUserMemberArguments::anyOfFamilyUnit(self::A_FAMILY_UNIT_ID);

        global $create_user_from_values_should_return;
        $create_user_from_values_should_return = [
            'name' => $newUserMemberArguments->memberName(),
            'login' => $newUserMemberArguments->username(),
            'password' => $newUserMemberArguments->password(),
            'custom_member_ref' => $newUserMemberArguments->custom_member_ref(),
            'nif' => $newUserMemberArguments->nif(),
            'address' => $newUserMemberArguments->address(),
            'city' => $newUserMemberArguments->city(),
            'zip' => $newUserMemberArguments->zip(),
            'phone1' => $newUserMemberArguments->phone1(),
            'phone2' => $newUserMemberArguments->phone2(),
            'web' => $newUserMemberArguments->web(),
            'notes' => $newUserMemberArguments->notes(),
            'active' => (int)$newUserMemberArguments->active(),
            'participant' => (int)$newUserMemberArguments->participant(),
            'adult' => (int)$newUserMemberArguments->adult(),
            'language' => $newUserMemberArguments->language(),
            'gui_theme' => $newUserMemberArguments->guiTheme(),
            'email' => $newUserMemberArguments->email()
        ];

        ob_start();
        create_user_member(self::A_FAMILY_UNIT_ID);
        $output = ob_get_clean();

        $this->assertUserAndMemberSavedInDatabaseSuccessfully(2, $newUserMemberArguments);
        $this->assertEmpty($output);
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

    /**
     * @param $expectedId
     * @param NewUserMemberArguments $newUserMemberArguments
     * @throws Exception
     */
    public function assertUserAndMemberSavedInDatabaseSuccessfully($expectedId, NewUserMemberArguments $newUserMemberArguments)
    {
        // Assert aixada_member
        $result = DBWrap::get_instance()->Select('*', 'aixada_member', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertValidMemberRow($expectedId, $rows, $newUserMemberArguments);

        // Assert aixada_user
        $result = DBWrap::get_instance()->Select('*', 'aixada_user', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertValidUserRow($expectedId, $rows, $newUserMemberArguments);

        // Assert aixada_user_role
        $result = DBWrap::get_instance()->Select('*', 'aixada_user_role', 'user_id = 2', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertSame('Checkout', $rows[0]['role']);
        $this->assertSame('Consumer', $rows[1]['role']);
    }

    /**
     * @param $memberId
     * @param $membersRows
     * @param NewUserMemberArguments $newUserMemberArguments
     * @throws Exception
     */
    public function assertValidMemberRow($memberId, $membersRows, NewUserMemberArguments $newUserMemberArguments)
    {
        $memberRow = current(array_filter($membersRows, function($row) use ($memberId) {
            return $row['id'] == $memberId;
        }));

        $this->assertEquals($memberId, $memberRow['id']);
        $this->assertSame($newUserMemberArguments->custom_member_ref(), $memberRow['custom_member_ref']);
        $this->assertEquals($newUserMemberArguments->familyUnitId(), $memberRow['uf_id']);
        $this->assertSame($newUserMemberArguments->memberName(), $memberRow['name']);
        $this->assertSame($newUserMemberArguments->address(), $memberRow['address']);
        $this->assertSame($newUserMemberArguments->nif(), $memberRow['nif']);
        $this->assertSame($newUserMemberArguments->zip(), $memberRow['zip']);
        $this->assertSame($newUserMemberArguments->city(), $memberRow['city']);
        $this->assertSame($newUserMemberArguments->phone1(), $memberRow['phone1']);
        $this->assertSame($newUserMemberArguments->phone2(), $memberRow['phone2']);
        $this->assertSame($newUserMemberArguments->web(), $memberRow['web']);
        $this->assertNull($memberRow['bank_name']);
        $this->assertNull($memberRow['bank_account']);
        $this->assertNull($memberRow['picture']);
        $this->assertSame($newUserMemberArguments->notes(), $memberRow['notes']);
        $this->assertEquals($newUserMemberArguments->active(), (bool)$memberRow['active']);
        $this->assertEquals($newUserMemberArguments->adult(), (bool)$memberRow['adult']);
        $this->assertEquals($newUserMemberArguments->participant(), (bool)$memberRow['participant']);
        $this->assertNotNull($memberRow['ts']);
    }

    /**
     * @param $expectedId
     * @param $userRow
     * @param NewUserMemberArguments $newUserMemberArguments
     * @throws Exception
     */
    public function assertValidUserRow($userId, $userRows, NewUserMemberArguments $newUserMemberArguments)
    {
        $userRow = current(array_filter($userRows, function($row) use ($userId) {
            return $row['id'] == $userId;
        }));

        $this->assertEquals($userId, $userRow['id']);
        $this->assertSame($newUserMemberArguments->username(), $userRow['login']);
        $this->assertSame($newUserMemberArguments->password(), $userRow['password']);
        $this->assertSame($newUserMemberArguments->email(), $userRow['email']);
        $this->assertEquals($newUserMemberArguments->familyUnitId(), $userRow['uf_id']);
        $this->assertEquals($userId, $userRow['member_id']);
        $this->assertNull($userRow['provider_id']);
        $this->assertSame($newUserMemberArguments->language(), $userRow['language']);
        $this->assertSame($newUserMemberArguments->guiTheme(), $userRow['gui_theme']);
        $this->assertNull($userRow['last_login_attempt']);
        $this->assertNull($userRow['last_successful_login']);
        $this->assertNotNull($userRow['created_on']);
    }

    private function initializeDefaultDatabase()
    {
        $this->db = connect_by_mysqli(
            get_config('db_host'),
            null,
            get_config('db_user'),
            get_config('db_password')
        );
        $this->db->query('DROP SCHEMA aixada');
        $this->db->query('CREATE SCHEMA aixada');
        $this->db->query('USE aixada');
        execute_sql_files($this->db, 'sql/', array(
            'aixada.sql',
            'setup/aixada_queries_all.sql',
            'setup/aixada_insert_defaults.sql',
            'setup/aixada_insert_default_user.sql'
        ));
        $this->db->query('USE aixada');
    }
}
