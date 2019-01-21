<?php

namespace Aixada\UseCase;

require_once __ROOT__ . 'src/Entity/Member.php';
require_once __ROOT__ . 'src/Entity/User.php';
require_once __ROOT__ . 'src/Repository/MemberRepository.php';
require_once __ROOT__ . 'src/Repository/UserRepository.php';
require_once __ROOT__ . 'php/utilities/useruf.php';

use Aixada\Entity\Member;
use Aixada\Entity\User;
use Aixada\Repository\MemberRepository;
use Aixada\Repository\UserRepository;

final class CreateMember
{
    /**
     * @var MemberRepository
     */
    private $members;

    /**
     * @var UserRepository
     */
    private $users;

    /**
     * CreateMember constructor.
     * @param MemberRepository $members
     * @param UserRepository $users
     */
    public function __construct(MemberRepository $members, UserRepository $users)
    {
        $this->members = $members;
        $this->users = $users;
    }

    /**
     * @param array $arguments
     * @throws \Exception
     */
    public function __invoke($arguments)
    {
        $this->assertUsernameNotInUse($arguments[1]);

        $newId = $this->members->nextId();

        $this->members->save($this->memberFromArguments($arguments, $newId));

        $this->users->save($this->userFromArguments($arguments, $newId));
    }

    private function assertUsernameNotInUse($username)
    {
        $login_exists = validate_field('aixada_user', 'login', $username);

        if($login_exists) {
            throw new \Exception("The login '" .$username. "' already exists. Please choose another one");
        }
    }

    /**
     * @param $arguments
     * @param $id
     * @return Member
     */
    private function memberFromArguments($arguments, $id)
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
    private function userFromArguments($arguments, $id)
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
    private function defaultRoles()
    {
        return ['Checkout', 'Consumer'];
    }

    /**
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    private function now()
    {
        return new \DateTimeImmutable();
    }
}
