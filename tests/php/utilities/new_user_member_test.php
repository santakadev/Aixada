<?php

require_once(__DIR__.'/../../../php/inc/database.php');
require_once(__DIR__.'/../../../php/inc/adminDatabase.php');
require_once(__DIR__.'/../../local_config/configuration_vars_test.php');

final class new_user_member_test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var mysqli
     */
    private $db;

    protected function setUp()
    {
        configuration_vars::TEST_set_test_instance(new configuration_vars_test());
        $this->initializeDefaultDatabase();
        $this->createFamilyUnitWithId(2);
    }

    protected function tearDown()
    {
        $this->db->close();
    }

    /** @test */
    public function should_add_new_members_to_family_units()
    {
        $result = DBWrap::get_instance()->Select('max(id) AS max_id', 'aixada_user', '', '');
        $initialDatabaseMaxId = $result->fetch_object()->max_id;

        $aNewMember = NewUserMemberArguments::anyOfFamilyUnit(FamilyUnitIdStub::ofId(1));
        $anotherNewMember = NewUserMemberArguments::anyOfFamilyUnit(FamilyUnitIdStub::ofId(2));
        $resultAddANewMember = $this->addNewUserMember($aNewMember);
        $resultAddAnotherNewMember = $this->addNewUserMember($anotherNewMember);

        $this->assertUserAndMemberSavedInDatabaseSuccessfully($initialDatabaseMaxId + 1, $aNewMember);
        $this->assertUserAndMemberSavedInDatabaseSuccessfully($initialDatabaseMaxId + 2, $anotherNewMember);
        $this->assertNull($resultAddANewMember);
        $this->assertNull($resultAddAnotherNewMember);
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
            $newUserMemberArguments->participant(),       // participant  bool,
            $newUserMemberArguments->adult(),             // adult        bool,
            $newUserMemberArguments->language(),          // language     char(5),
            $newUserMemberArguments->guiTheme(),          // gui_theme    string(50),
            $newUserMemberArguments->email()              // email        string(100),
        );
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

    private function createFamilyUnitWithId($familyUnitId)
    {
        $statament = $this->db->prepare('CALL create_uf("family unit 2", ?, 1)');
        $statament->bind_param('i', $familyUnitId);
        $statament->execute();
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
     * @param int $familyUnitId
     * @return NewUserMemberArguments
     */
    public static function anyOfFamilyUnit($familyUnitId)
    {
        return new self(
            UsernameStub::any(),
            PasswordStub::any(),
            $familyUnitId,
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