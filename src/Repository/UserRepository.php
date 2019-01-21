<?php

namespace Aixada\Repository;

use Aixada\Entity\User;

interface UserRepository
{
    /**
     * @param User $user
     */
    public function save(User $user);
}
