<?php

namespace Aixada\UseCase;

require_once __DIR__ . '/CreateMember.php';
require_once __ROOT__ . 'src/Entity/Member.php';
require_once __ROOT__ . 'src/Entity/User.php';
require_once __ROOT__ . 'src/Repository/MemberRepository.php';
require_once __ROOT__ . 'src/Repository/UserRepository.php';

use Aixada\Repository\MemberRepository;
use Aixada\Repository\UserRepository;
use DBWrap;

final class TransactionalCreateMember
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
     * @var DBWrap
     */
    private $connection;

    /**
     * CreateMember constructor.
     * @param MemberRepository $members
     * @param UserRepository $users
     */
    public function __construct(MemberRepository $members, UserRepository $users)
    {
        $this->members = $members;
        $this->users = $users;
        $this->connection = DBWrap::get_instance();
    }

    /**
     * @param array $arguments
     * @throws \Exception
     */
    public function __invoke($arguments)
    {
        $this->connection->start_transaction();

        $nonTransactionalCreateMember = new CreateMember($this->members, $this->users);
        $nonTransactionalCreateMember->__invoke($arguments);

        $this->connection->commit();
    }
}
