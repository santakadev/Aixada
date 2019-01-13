<?php

namespace Aixada\UseCase;

require_once __ROOT__ . 'src/Repository/MySqlMemberRepository.php';

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

        $mySqlMemberRepository = new MySqlMemberRepository();
        $mySqlMemberRepository->save($newId, $arguments);

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
}
