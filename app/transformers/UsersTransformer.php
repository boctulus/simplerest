<?php

namespace simplerest\transformers;

use simplerest\core\controllers\Controller;

class UsersTransformer implements \simplerest\core\interfaces\ITransformer
{
	public function transform(object $user, Controller $controller = NULL)
    {
        return [
			'id' => $user->id,
			'username' => $user->username,
			'is_active' => $user->is_active,
			'email' => $user->email,
			'confirmed_email' => $user->confirmed_email,
			'password' => $user->password,
			'firstname' => 'Mr. ' . $user->firstname,
			'lastname' => $user->lastname,
			'full_name' => "{$user->firstname} {$user->lastname}",
			'deleted_at' => $user->deleted_at,
			'belongs_to' => $user->belongs_to
        ];
	}
}







