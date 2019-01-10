<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS);

require_once(__DIR__.'/../../../php/inc/database.php');
require_once(__DIR__.'/../../../php/inc/adminDatabase.php');
require_once(__DIR__.'/../../local_config/configuration_vars_test.php');

final class new_user_member_test extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        configuration_vars::TEST_set_test_instance(new configuration_vars_test());

        $db = connect_by_mysqli(
            get_config('db_host'),
            null,
            get_config('db_user'),
            get_config('db_password')
        );
        $db->query('DROP SCHEMA aixada');
        $db->query('CREATE SCHEMA aixada');
        $db->query('USE aixada');
        execute_sql_files($db, 'sql/', array(
            'aixada.sql',
            'setup/aixada_insert_defaults.sql',
            'setup/aixada_insert_default_user.sql'
        ));
    }

    public function test_new_user_member()
    {
        $result = DBWrap::get_instance()->Select('max(id) AS max_id', 'aixada_user', '', '');
        $maxId = $result->fetch_object()->max_id;

        $username = UsernameStub::any();
        $password = PasswordStub::any();
        $familyUnitId = FamilyUnitIdStub::ofId(1);
        $custom_member_ref = CustomMemberRefStub::any();
        $memberName = MemberNameStub::any();
        $nif = NifStub::any();
        $address = AddressStub::any();
        $city = CityStub::any();
        $zip = ZipStub::any();
        $phone1 = PhoneStub::any();
        $phone2 = PhoneStub::any();
        $web = WebStub::any();
        $notes = NotesStub::any();
        $active = BoolStub::any();
        $adult = BoolStub::any();
        $participant = BoolStub::any();
        $language = LanguageStub::any();
        $guiTheme = GuiThemeStub::any();
        $email = EmailStub::any();
        do_stored_query(
            'new_user_member',
            $username,          // login        string(50)
            $password,          // password     string(255)
            $familyUnitId,      // uf_id        int,
            $custom_member_ref, // customer_ref string(100),
            $memberName,        // name         string(255),
            $nif,               // nif          string(15),
            $address,           // address      string(255),
            $city,              // city         string(255),
            $zip,               // zip          string(10),
            $phone1,            // phone1       string(50),
            $phone2,            // phone2       string(50),
            $web,               // web          string(255),
            $notes,             // notes        string(text),
            $active,            // active       bool,
            $adult,             // participant  bool,
            $participant,       // adult        bool,
            $language,          // language     char(5),
            $guiTheme,          // gui_theme    string(50),
            $email              // email        string(100),
        );

        // Assert aixada_member
        $result = DBWrap::get_instance()->Select('*', 'aixada_member', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newMemberRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertEquals($maxId + 1, $newMemberRow['id']);
        $this->assertSame($custom_member_ref, $newMemberRow['custom_member_ref']);
        $this->assertEquals($familyUnitId, $newMemberRow['uf_id']);
        $this->assertSame($memberName, $newMemberRow['name']);
        $this->assertSame($address, $newMemberRow['address']);
        $this->assertSame($nif, $newMemberRow['nif']);
        $this->assertSame($zip, $newMemberRow['zip']);
        $this->assertSame($city, $newMemberRow['city']);
        $this->assertSame($phone1, $newMemberRow['phone1']);
        $this->assertSame($phone2, $newMemberRow['phone2']);
        $this->assertSame($web, $newMemberRow['web']);
        $this->assertNull($newMemberRow['bank_name']);
        $this->assertNull($newMemberRow['bank_account']);
        $this->assertNull($newMemberRow['picture']);
        $this->assertSame($notes, $newMemberRow['notes']);
        $this->assertEquals($active, (bool)$newMemberRow['active']);
        $this->assertEquals($adult, (bool)$newMemberRow['participant']);
        $this->assertEquals($participant, (bool)$newMemberRow['adult']);
        $this->assertNotNull($newMemberRow['ts']);
        $this->assertLessThanOrEqual((int)date('U'), (new \DateTimeImmutable($newMemberRow['ts']))->getTimestamp());

        // Assert aixada_user
        $result = DBWrap::get_instance()->Select('*', 'aixada_user', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newUserRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertSame('2', $newUserRow['id']);
        $this->assertSame($username, $newUserRow['login']);
        $this->assertSame($password, $newUserRow['password']);
        $this->assertSame($email, $newUserRow['email']);
        $this->assertEquals($familyUnitId, $newUserRow['uf_id']);
        $this->assertSame('2', $newUserRow['member_id']);
        $this->assertNull($newUserRow['provider_id']);
        $this->assertSame($language, $newUserRow['language']);
        $this->assertSame($guiTheme, $newUserRow['gui_theme']);
        $this->assertNull($newUserRow['last_login_attempt']);
        $this->assertNull($newUserRow['last_successful_login']);
        $this->assertNotNull($newUserRow['created_on']);
        $this->assertLessThanOrEqual((int)date('U'), (new \DateTimeImmutable($newUserRow['created_on']))->getTimestamp());

        // Assert aixada_user_role
        $result = DBWrap::get_instance()->Select('*', 'aixada_user_role', 'user_id = 2', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $this->assertCount(2, $rows);
        $this->assertSame('Checkout', $rows[0]['role']);
        $this->assertSame('Consumer', $rows[1]['role']);
    }
}

class BoolStub
{
    public static function any()
    {
        return (bool)rand(0, 1);
    }
}

class StringStub
{
    /**
     * @param int $length
     * @return string
     */
    public static function alphanumericOfLength($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * @param int $min
     * @param int $max
     * @return string
     */
    public static function alphanumericOfLengthBetween($min, $max)
    {
        return self::alphanumericOfLength(rand($min, $max));
    }
}

class UsernameStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 50);
    }
}

class PasswordStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 255);
    }
}

class FamilyUnitIdStub
{
    /**
     * @param int $familyUnitId
     * @return int
     */
    public static function ofId($familyUnitId)
    {
        return $familyUnitId;
    }
}

class CustomMemberRefStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 100);
    }
}

class MemberNameStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 255);
    }
}

class NifStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 15);
    }
}

class AddressStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 255);
    }
}

class CityStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 255);
    }
}

class ZipStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 10);
    }
}

class PhoneStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 50);
    }
}

class WebStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 255);
    }
}

class NotesStub
{
    /**
     * @return string
     */
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 4096);
    }
}

class LanguageStub
{
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 5);
    }
}

class GuiThemeStub
{
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 50);
    }
}

class EmailStub
{
    public static function any()
    {
        return StringStub::alphanumericOfLengthBetween(1, 100);
    }
}