<?php

namespace Boctulus\Simplerest\Models\main;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\UsersSchema;

class UsersModel extends MyModel
{
	protected $hidden   = [	'password' ];
	protected $not_fillable = ['confirmed_email', 'is_active'];

	static public $email    = 'email';
	static public $username = 'username';
	static public $password = 'password';
	static public $confirmed_email = 'confirmed_email';
	static public $is_active = 'is_active';

    function __construct(bool $connect = false){
		$this->registerInputMutator(static::$password, function($password){
			if (!is_string($password) || $password === '') {
				throw new \InvalidArgumentException('Password cannot be empty');
			}

			if (password_get_info($password)['algoName'] !== 'unknown') {
				return $password;
			}

			return password_hash($password, PASSWORD_DEFAULT);
		}, function($op, $dato){
			return ($dato !== null && $dato !== '');
		});

        parent::__construct($connect, UsersSchema::class);
	}

	public function findPasswordResetCandidate(string $email): ?array
	{
		$user = $this
			->assoc()
			->unhide([static::$password])
			->where([
				static::$email => strtolower(trim($email)),
				static::$is_active => 1,
			])
			->first();

		return is_array($user) ? $user : null;
	}

	public function findActiveForPasswordReset(int $userId): ?array
	{
		$user = $this
			->assoc()
			->unhide([static::$password])
			->where([
				'id' => $userId,
				static::$is_active => 1,
			])
			->first();

		return is_array($user) ? $user : null;
	}

	public function updatePasswordFromReset(int $userId, string $password): bool
	{
		return (bool) $this
			->find($userId)
			->update([
				static::$password => $password,
			]);
	}
	
	// Hooks
	function onUpdating(&$data) {
		if ($this->isDirty('email')) {
			$this->fill(['confirmed_email'])->update(['confirmed_email' => 0]);
		}
	}
}

