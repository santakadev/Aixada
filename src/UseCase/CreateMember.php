<?php

namespace Aixada\UseCase;

require_once __ROOT__ . 'src/Entity/Member.php';
require_once __ROOT__ . 'src/Entity/User.php';
require_once __ROOT__ . 'src/Repository/MySqlMemberRepository.php';
require_once __ROOT__ . 'src/Repository/MySqlUserRepository.php';

use Aixada\Entity\Member;
use Aixada\Entity\User;
use Aixada\Repository\MySqlMemberRepository;
use Aixada\Repository\MySqlUserRepository;
use DBWrap;

final class CreateMember
{
    /**
     * @var MySqlMemberRepository
     */
    private $members;

    /**
     * @var MySqlUserRepository
     */
    private $users;

    public function __construct()
    {
        $this->members = new MySqlMemberRepository();
        $this->users = new MySqlUserRepository();
    }

    /**
     * @param array $arguments
     * @throws \InternalException
     */
    public function __invoke($arguments)
    {
        $database = DBWrap::get_instance();
        $database->start_transaction();

        $newId = $this->members->nextId();

        $this->members->save($this->memberFromArguments($arguments, $newId));

        $this->users->save($this->userFromArguments($arguments, $newId));

        $database->Insert([
            'table' => 'aixada_user_role',
            'user_id' => $newId,
            'role' => 'Checkout',
        ]);

        $database->Insert([
            'table' => 'aixada_user_role',
            'user_id' => $newId,
            'role' => 'Consumer',
        ]);

        $database->commit();
    }

    /**
     * @param $arguments
     * @param $newId
     * @return Member
     */
    public function memberFromArguments($arguments, $newId)
    {
        $member = new Member(
            $newId,
            $arguments[3],
            $arguments[4],
            $arguments[5],
            $arguments[6],
            $arguments[7],
            $arguments[8],
            $arguments[9],
            $arguments[10],
            $arguments[11],
            $arguments[12],
            $arguments[13],
            $arguments[14],
            $arguments[15],
            $arguments[16]
        );
        return $member;
    }

    /**
     * @param $arguments
     * @param $newId
     * @return User
     * @throws \Exception
     */
    public function userFromArguments($arguments, $newId)
    {
        $user = new User(
            $newId,
            $arguments[1],
            $arguments[2],
            $arguments[3],
            $newId,
            $arguments[17],
            $arguments[18],
            $arguments[19],
            new \DateTimeImmutable()
        );
        return $user;
    }
}
