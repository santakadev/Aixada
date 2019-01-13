<?php

namespace Aixada\UseCase;

require_once __ROOT__ . 'src/Entity/Member.php';
require_once __ROOT__ . 'src/Repository/MySqlMemberRepository.php';

use Aixada\Entity\Member;
use Aixada\Repository\MySqlMemberRepository;
use DBWrap;

final class CreateMember
{
    /**
     * @param array $arguments
     * @throws \InternalException
     */
    public function __invoke($arguments)
    {
        $database = DBWrap::get_instance();
        $database->start_transaction();

        $result = DBWrap::get_instance()->Select('max(id)+1 AS newId', 'aixada_user', 'id < 1000', '');
        $newId = $result->fetch_object()->newId;

        (new MySqlMemberRepository())->save($this->memberFromArguments($arguments, $newId));

        $database->Insert([
            'table' => 'aixada_user',
            'id' => $newId,
            'login' => $arguments[1],
            'password' => $arguments[2],
            'uf_id' => $arguments[3],
            'member_id' => $newId,
            'language' => $arguments[17],
            'gui_theme' => $arguments[18],
            'email' => $arguments[19],
            'created_on' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);

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
}
