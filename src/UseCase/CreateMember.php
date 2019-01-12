<?php

namespace Aixada\UseCase;

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

        $database->Insert([
            'table' => 'aixada_member',
            'id' => $newId,
            'uf_id' => $arguments[3],
            'custom_member_ref' => $arguments[4],
            'name' => $arguments[5],
            'nif' => $arguments[6],
            'address' => $arguments[7],
            'city' => $arguments[8],
            'zip' => $arguments[9],
            'phone1' => $arguments[10],
            'phone2' => $arguments[11],
            'web' => $arguments[12],
            'notes' => $arguments[13],
            'active' => $arguments[14],
            'participant' => $arguments[15],
            'adult' => $arguments[16],
        ]);

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
