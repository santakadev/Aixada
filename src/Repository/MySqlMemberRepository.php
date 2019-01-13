<?php

namespace Aixada\Repository;

use DBWrap;

final class MySqlMemberRepository
{
    public function save($id, $arguments)
    {
        $connection = DBWrap::get_instance();
        $connection->Insert([
            'table' => 'aixada_member',
            'id' => $id,
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
    }
}