<?php

namespace Aixada\Repository;

use DBWrap;

final class MySqlUserRepository
{
    public function save($id, $arguments)
    {
        $connection = DBWrap::get_instance();
        $connection->Insert([
            'table' => 'aixada_user',
            'id' => $id,
            'login' => $arguments[1],
            'password' => $arguments[2],
            'uf_id' => $arguments[3],
            'member_id' => $id,
            'language' => $arguments[17],
            'gui_theme' => $arguments[18],
            'email' => $arguments[19],
            'created_on' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }
}