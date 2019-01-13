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

    /**
     * @var DBWrap
     */
    private $connection;

    public function __construct()
    {
        $this->members = new MySqlMemberRepository();
        $this->users = new MySqlUserRepository();
        $this->connection = DBWrap::get_instance();
    }

    /**
     * @param array $arguments
     * @throws \Exception
     */
    public function __invoke($arguments)
    {
        $this->connection->start_transaction();

        $newId = $this->members->nextId();

        $this->members->save($this->memberFromArguments($arguments, $newId));

        $this->users->save($this->userFromArguments($arguments, $newId));

        $this->connection->commit();
    }

    /**
     * @param $arguments
     * @param $id
     * @return Member
     */
    public function memberFromArguments($arguments, $id)
    {
        return new Member(
            $id,
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
    }

    /**
     * @param $arguments
     * @param $id
     * @return User
     * @throws \Exception
     */
    public function userFromArguments($arguments, $id)
    {
        return new User(
            $id,
            $arguments[1],
            $arguments[2],
            $this->defaultRoles(),
            $arguments[3],
            $id,
            $arguments[17],
            $arguments[18],
            $arguments[19],
            $this->now()
        );
    }

    /**
     * @return array
     */
    public function defaultRoles()
    {
        return ['Checkout', 'Consumer'];
    }

    /**
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    public function now()
    {
        return new \DateTimeImmutable();
    }
}
