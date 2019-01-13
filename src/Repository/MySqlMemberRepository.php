<?php

namespace Aixada\Repository;

use Aixada\Entity\Member;
use DBWrap;

final class MySqlMemberRepository
{
    public function save(Member $member)
    {
        $connection = DBWrap::get_instance();
        $connection->Insert([
            'table' => 'aixada_member',
            'id' => $member->id(),
            'uf_id' => $member->familyUnitId(),
            'custom_member_ref' => $member->customMemberRef(),
            'name' => $member->name(),
            'nif' => $member->nif(),
            'address' => $member->address(),
            'city' => $member->city(),
            'zip' => $member->zip(),
            'phone1' => $member->phone1(),
            'phone2' => $member->phone2(),
            'web' => $member->web(),
            'notes' => $member->notes(),
            'active' => $member->active(),
            'participant' => $member->participant(),
            'adult' => $member->adult(),
        ]);
    }
}