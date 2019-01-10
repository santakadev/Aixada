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

        $newUserMemberArguments = NewUserMemberArguments::any();
        $this->addNewUserMember($newUserMemberArguments);

        // Assert aixada_member
        $result = DBWrap::get_instance()->Select('*', 'aixada_member', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newMemberRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertEquals($maxId + 1, $newMemberRow['id']);
        $this->assertSame($newUserMemberArguments->custom_member_ref(), $newMemberRow['custom_member_ref']);
        $this->assertEquals($newUserMemberArguments->familyUnitId(), $newMemberRow['uf_id']);
        $this->assertSame($newUserMemberArguments->memberName(), $newMemberRow['name']);
        $this->assertSame($newUserMemberArguments->address(), $newMemberRow['address']);
        $this->assertSame($newUserMemberArguments->nif(), $newMemberRow['nif']);
        $this->assertSame($newUserMemberArguments->zip(), $newMemberRow['zip']);
        $this->assertSame($newUserMemberArguments->city(), $newMemberRow['city']);
        $this->assertSame($newUserMemberArguments->phone1(), $newMemberRow['phone1']);
        $this->assertSame($newUserMemberArguments->phone2(), $newMemberRow['phone2']);
        $this->assertSame($newUserMemberArguments->web(), $newMemberRow['web']);
        $this->assertNull($newMemberRow['bank_name']);
        $this->assertNull($newMemberRow['bank_account']);
        $this->assertNull($newMemberRow['picture']);
        $this->assertSame($newUserMemberArguments->notes(), $newMemberRow['notes']);
        $this->assertEquals($newUserMemberArguments->active(), (bool)$newMemberRow['active']);
        $this->assertEquals($newUserMemberArguments->adult(), (bool)$newMemberRow['participant']);
        $this->assertEquals($newUserMemberArguments->participant(), (bool)$newMemberRow['adult']);
        $this->assertNotNull($newMemberRow['ts']);
        $this->assertLessThanOrEqual((int)date('U'), (new \DateTimeImmutable($newMemberRow['ts']))->getTimestamp());

        // Assert aixada_user
        $result = DBWrap::get_instance()->Select('*', 'aixada_user', '', '');
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $newUserRow = end($rows);
        $this->assertCount(2, $rows);
        $this->assertSame('2', $newUserRow['id']);
        $this->assertSame($newUserMemberArguments->username(), $newUserRow['login']);
        $this->assertSame($newUserMemberArguments->password(), $newUserRow['password']);
        $this->assertSame($newUserMemberArguments->email(), $newUserRow['email']);
        $this->assertEquals($newUserMemberArguments->familyUnitId(), $newUserRow['uf_id']);
        $this->assertSame('2', $newUserRow['member_id']);
        $this->assertNull($newUserRow['provider_id']);
        $this->assertSame($newUserMemberArguments->language(), $newUserRow['language']);
        $this->assertSame($newUserMemberArguments->guiTheme(), $newUserRow['gui_theme']);
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

    private function addNewUserMember(NewUserMemberArguments $newUserMemberArguments)
    {
        do_stored_query(
            'new_user_member',
            $newUserMemberArguments->username(),          // login        string(50)
            $newUserMemberArguments->password(),          // password     string(255)
            $newUserMemberArguments->familyUnitId(),      // uf_id        int,
            $newUserMemberArguments->custom_member_ref(), // customer_ref string(100),
            $newUserMemberArguments->memberName(),        // name         string(255),
            $newUserMemberArguments->nif(),               // nif          string(15),
            $newUserMemberArguments->address(),           // address      string(255),
            $newUserMemberArguments->city(),              // city         string(255),
            $newUserMemberArguments->zip(),               // zip          string(10),
            $newUserMemberArguments->phone1(),            // phone1       string(50),
            $newUserMemberArguments->phone2(),            // phone2       string(50),
            $newUserMemberArguments->web(),               // web          string(255),
            $newUserMemberArguments->notes(),             // notes        string(text),
            $newUserMemberArguments->active(),            // active       bool,
            $newUserMemberArguments->adult(),             // participant  bool,
            $newUserMemberArguments->participant(),       // adult        bool,
            $newUserMemberArguments->language(),          // language     char(5),
            $newUserMemberArguments->guiTheme(),          // gui_theme    string(50),
            $newUserMemberArguments->email()              // email        string(100),
        );
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

class NewUserMemberArguments
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var int */
    private $familyUnitId;

    /** @var string */
    private $custom_member_ref;

    /** @var string */
    private $memberName;

    /** @var string */
    private $nif;

    /** @var string */
    private $address;

    /** @var string */
    private $city;

    /** @var string */
    private $zip;

    /** @var string */
    private $phone1;

    /** @var string */
    private $phone2;

    /** @var string */
    private $web;

    /** @var string */
    private $notes;

    /** @var bool */
    private $active;

    /** @var bool */
    private $adult;

    /** @var bool */
    private $participant;

    /** @var string */
    private $language;

    /** @var string */
    private $guiTheme;

    /** @var string */
    private $email;

    /**
     * NewUserMemberArguments constructor.
     * @param string $username
     * @param string $password
     * @param int $familyUnitId
     * @param string $custom_member_ref
     * @param string $memberName
     * @param string $nif
     * @param string $address
     * @param string $city
     * @param string $zip
     * @param string $phone1
     * @param string $phone2
     * @param string $web
     * @param string $notes
     * @param bool $active
     * @param bool $adult
     * @param bool $participant
     * @param string $language
     * @param string $guiTheme
     * @param string $email
     */
    public function __construct(
        $username,
        $password,
        $familyUnitId,
        $custom_member_ref,
        $memberName,
        $nif,
        $address,
        $city,
        $zip,
        $phone1,
        $phone2,
        $web,
        $notes,
        $active,
        $adult,
        $participant,
        $language,
        $guiTheme,
        $email
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->familyUnitId = $familyUnitId;
        $this->custom_member_ref = $custom_member_ref;
        $this->memberName = $memberName;
        $this->nif = $nif;
        $this->address = $address;
        $this->city = $city;
        $this->zip = $zip;
        $this->phone1 = $phone1;
        $this->phone2 = $phone2;
        $this->web = $web;
        $this->notes = $notes;
        $this->active = $active;
        $this->adult = $adult;
        $this->participant = $participant;
        $this->language = $language;
        $this->guiTheme = $guiTheme;
        $this->email = $email;
    }

    /**
     * @return NewUserMemberArguments
     */
    public static function any()
    {
        return new self(
            UsernameStub::any(),
            PasswordStub::any(),
            FamilyUnitIdStub::ofId(1),
            CustomMemberRefStub::any(),
            MemberNameStub::any(),
            NifStub::any(),
            AddressStub::any(),
            CityStub::any(),
            ZipStub::any(),
            PhoneStub::any(),
            PhoneStub::any(),
            WebStub::any(),
            NotesStub::any(),
            BoolStub::any(),
            BoolStub::any(),
            BoolStub::any(),
            LanguageStub::any(),
            GuiThemeStub::any(),
            EmailStub::any()
        );
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function familyUnitId()
    {
        return $this->familyUnitId;
    }

    /**
     * @return string
     */
    public function custom_member_ref()
    {
        return $this->custom_member_ref;
    }

    /**
     * @return string
     */
    public function memberName()
    {
        return $this->memberName;
    }

    /**
     * @return string
     */
    public function nif()
    {
        return $this->nif;
    }

    /**
     * @return string
     */
    public function address()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function city()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function zip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function phone1()
    {
        return $this->phone1;
    }

    /**
     * @return string
     */
    public function phone2()
    {
        return $this->phone2;
    }

    /**
     * @return string
     */
    public function web()
    {
        return $this->web;
    }

    /**
     * @return string
     */
    public function notes()
    {
        return $this->notes;
    }

    /**
     * @return bool
     */
    public function active()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function adult()
    {
        return $this->adult;
    }

    /**
     * @return bool
     */
    public function participant()
    {
        return $this->participant;
    }

    /**
     * @return string
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function guiTheme()
    {
        return $this->guiTheme;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }
}