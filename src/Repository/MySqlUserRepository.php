<?php

namespace Aixada\Repository;

require_once __DIR__ . '/UserRepository.php';

use Aixada\Entity\User;
use DBWrap;

final class MySqlUserRepository implements UserRepository
{
    public function save(User $user)
    {
        $connection = DBWrap::get_instance();
        $connection->Insert([
            'table' => 'aixada_user',
            'id' => $user->id(),
            'login' => $user->login(),
            'password' => $user->password(),
            'uf_id' => $user->familyUnitId(),
            'member_id' => $user->memberId(),
            'language' => $user->language(),
            'gui_theme' => $user->guiTheme(),
            'email' => $user->email(),
            'created_on' => $user->createdOn()->format('Y-m-d H:i:s')
        ]);

        foreach ($user->roles() as $role) {
            $connection->Insert([
                'table' => 'aixada_user_role',
                'user_id' => $user->id(),
                'role' => $role,
            ]);
        }
    }
}