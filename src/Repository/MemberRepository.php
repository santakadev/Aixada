<?php

namespace Aixada\Repository;

use Aixada\Entity\Member;

interface MemberRepository
{
    /**
     * @param Member $member
     */
    public function save(Member $member);

    /**
     * @return int
     */
    public function nextId();
}
