<?php

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

class UsersCommand implements ICommand
{
	use CommandTrait;

	protected $usersTable;
	protected $__id;
	protected $__email;
	protected $__username;
	protected $__password;
	protected $__confirmedEmail;
	protected $__isActive;

	public function __construct(){
		$this->usersTable      = get_users_table();
		$this->__id            = get_id_name($this->usersTable);
		$this->__email         = 'email';
		$this->__username      = 'username';
		$this->__password      = 'password';
		$this->__confirmedEmail = 'confirmed_email';
		$this->__isActive      = 'is_active';
	}

	public function handle($args){
		if (empty($args)){
			$this->help();
			return;
		}

		$method = str_replace('-', '_', array_shift($args));

		$aliases = [
			'create'   => 'create_user',
			'new'      => 'create_user',
			'add'      => 'create_user',
			'delete'   => 'delete_user',
			'del'      => 'delete_user',
			'rm'       => 'delete_user',
			'remove'   => 'delete_user',
			'del_user' => 'delete_user',
			'rm_user'  => 'delete_user',
			'ls'       => 'list_users',
			'list'     => 'list_users',
			'ls_users' => 'list_users',
			'show'     => 'show_user',
			'get'      => 'show_user',
			'info'     => 'show_user',
		];

		if (isset($aliases[$method])){
			$method = $aliases[$method];
		}

		if (!method_exists($this, $method)){
			echo "Error: Comando '{$method}' no existe.\n";
			echo "Ejecuta 'php com users help' para ver los comandos disponibles.\n";
			return;
		}

		call_user_func_array([$this, $method], $args);
	}

	protected function parseOptions(array $args): array{
		$options = [];

		foreach ($args as $arg){
			if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $m)){
				$key           = str_replace('-', '_', $m[1]);
				$options[$key] = trim($m[2], '"\'');
			} elseif (preg_match('/^--([^=:]+)$/', $arg, $m)){
				$key           = str_replace('-', '_', $m[1]);
				$options[$key] = true;
			}
		}

		return $options;
	}

	protected function extractEmail(array $args): array{
		$opts = $this->parseOptions($args);

		if (!empty($opts['email'])){
			return [$opts['email'], $opts];
		}

		foreach ($args as $arg){
			if (!str_starts_with($arg, '--')){
				return [$arg, $opts];
			}
		}

		return [null, $opts];
	}

	protected function getUserByEmail(string $email){
		return DB::table($this->usersTable)
			->unhide(['password'])
			->where([$this->__email => $email])
			->first();
	}

	protected function updateUser(int $id, array $data): void{
		$parts = [];
		$vals  = [];

		foreach ($data as $col => $val){
			$parts[] = "`{$col}` = ?";
			$vals[]  = $val;
		}

		$set = implode(', ', $parts);
		$vals[] = $id;

		DB::statement(
			"UPDATE `{$this->usersTable}` SET {$set} WHERE `{$this->__id}` = ?",
			$vals
		);
	}

	// ==================================================================
	//  create-user
	// ==================================================================
	public function create_user(...$options){
		$opts        = $this->parseOptions($options);
		$email       = $opts['email'] ?? null;
		$password    = $opts['password'] ?? null;
		$username    = $opts['username'] ?? $opts['user'] ?? null;
		$firstname   = $opts['firstname'] ?? $opts['first_name'] ?? null;
		$lastname    = $opts['lastname'] ?? $opts['last_name'] ?? null;
		$displayName = $opts['display_name'] ?? $opts['displayName'] ?? null;

		if (!$email || !$password){
			echo "Error: --email y --password son requeridos.\n";
			return;
		}

		if (!$username){
			$username = explode('@', $email)[0];
		}

		if ($this->getUserByEmail($email)){
			echo "Error: El email '{$email}' ya está registrado.\n";
			return;
		}

		$data = [
			$this->__email        => $email,
			$this->__password     => $password,
			$this->__username     => $username,
			'firstname'           => $firstname ?? $displayName ?? $username,
			'lastname'            => $lastname ?? '',
			$this->__isActive      => 1,
			$this->__confirmedEmail => 0,
			'created_at'          => date('Y-m-d H:i:s'),
		];

		$id = DB::table($this->usersTable)->create($data);

		if ($id){
			echo "✓ Usuario creado exitosamente\n";
			echo "  ID:       {$id}\n";
			echo "  Email:    {$email}\n";
			echo "  Username: {$username}\n";

			$role = $opts['role'] ?? null;
			if ($role){
				$this->assignRole($id, $role);
				echo "  Rol:      {$role}\n";
			}
		} else {
			echo "✗ Error al crear usuario.\n";
		}
	}

	// ==================================================================
	//  delete-user
	// ==================================================================
	public function delete_user(...$options){
		[$email, $opts] = $this->extractEmail($options);
		$force = !empty($opts['force']);

		if (!$email){
			echo "Error: Debes proporcionar un email.\n";
			echo "Uso: php com users delete-user <email> [--force]\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		if (!$force){
			echo "¿Estás seguro de eliminar a '{$email}'? Agrega --force para confirmar.\n";
			return;
		}

		$this->updateUser($user[$this->__id], ['deleted_at' => date('Y-m-d H:i:s')]);

		echo "✓ Usuario '{$email}' eliminado (soft delete).\n";
	}

	// ==================================================================
	//  disable-user
	// ==================================================================
	public function disable_user(...$options){
		[$email] = $this->extractEmail($options);

		if (!$email){
			echo "Error: Debes proporcionar un email.\n";
			echo "Uso: php com users disable-user <email>\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$this->updateUser($user[$this->__id], [$this->__isActive => 0]);

		echo "✓ Usuario '{$email}' deshabilitado.\n";
	}

	// ==================================================================
	//  enable-user
	// ==================================================================
	public function enable_user(...$options){
		[$email] = $this->extractEmail($options);

		if (!$email){
			echo "Error: Debes proporcionar un email.\n";
			echo "Uso: php com users enable-user <email>\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$this->updateUser($user[$this->__id], [$this->__isActive => 1]);

		echo "✓ Usuario '{$email}' habilitado.\n";
	}

	// ==================================================================
	//  list-users
	// ==================================================================
	public function list_users(...$options){
		$opts         = $this->parseOptions($options);
		$roleFilter   = $opts['role'] ?? null;
		$disabledOnly = !empty($opts['disabled_only']);

		$query = DB::table($this->usersTable)
			->select([
				$this->usersTable . '.' . $this->__id,
				$this->usersTable . '.' . $this->__email,
				'firstname',
				'lastname',
				$this->__isActive,
				$this->__confirmedEmail,
			]);

		if ($disabledOnly){
			$query = $query->where($this->__isActive, 0);
		}

		if ($roleFilter){
			$query = $query
				->join('user_roles', $this->usersTable . '.' . $this->__id, '=', 'user_roles.user_id')
				->join('roles', 'user_roles.role_id', '=', 'roles.id')
				->where('roles.name', $roleFilter);
		}

		$users = $query->get();

		if (empty($users)){
			echo "No se encontraron usuarios.\n";
			return;
		}

		echo str_pad('', 65, '=') . "\n";

		foreach ($users as $user){
			$isActive = ($user[$this->__isActive] ?? 1) ? '✓' : '✗';
			$emailVer = ($user[$this->__confirmedEmail] ?? 0) ? '✓' : '✗';
			$name     = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
			$id       = $user[$this->__id];

			echo "  {$isActive} [{$id}] {$user[$this->__email]}\n";

			if ($name){
				echo "     Nombre:   {$name}\n";
			}

			echo "     Verificado: {$emailVer}\n";
			echo "\n";
		}
	}

	// ==================================================================
	//  set-password
	// ==================================================================
	public function set_password(...$options){
		$opts    = $this->parseOptions($options);
		$email   = $opts['email'] ?? null;
		$newPass = $opts['password'] ?? null;

		if (!$email || !$newPass){
			echo "Error: --email y --password son requeridos.\n";
			echo "Uso: php com users set-password --email=user@example.com --password=newPass123\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$pHash = password_hash($newPass, PASSWORD_DEFAULT);
		$this->updateUser($user[$this->__id], [$this->__password => $pHash]);

		echo "✓ Contraseña actualizada para '{$email}'.\n";
	}

	// ==================================================================
	//  set-role
	// ==================================================================
	public function set_role(...$options){
		$opts  = $this->parseOptions($options);
		$email = $opts['email'] ?? null;
		$role  = $opts['role'] ?? null;

		if (!$email || !$role){
			echo "Error: --email y --role son requeridos.\n";
			echo "Uso: php com users set-role --email=user@example.com --role=admin\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$userId = $user[$this->__id];

		$roleRow = DB::table('roles')->where(['name' => $role])->first();

		if (!$roleRow){
			echo "✗ Rol '{$role}' no existe en la tabla roles.\n";
			return;
		}

		$existing = DB::table('user_roles')
			->where(['user_id' => $userId])
			->first();

		if ($existing){
			DB::table('user_roles')
				->where(['user_id' => $userId])
				->update(['role_id' => $roleRow['id']]);
		} else {
			DB::table('user_roles')->insert([
				'user_id' => $userId,
				'role_id' => $roleRow['id'],
			]);
		}

		echo "✓ Rol '{$role}' asignado a '{$email}'.\n";
	}

	// ==================================================================
	//  show-user
	// ==================================================================
	public function show_user(...$options){
		[$email] = $this->extractEmail($options);

		if (!$email){
			echo "Error: Debes proporcionar un email.\n";
			echo "Uso: php com users show-user <email>\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$userId   = $user[$this->__id];
		$isActive = ($user[$this->__isActive] ?? 1) ? '✓' : '✗';
		$emailVer = ($user[$this->__confirmedEmail] ?? 0) ? '✓' : '✗';

		echo "Información del usuario:\n";
		echo str_pad('', 40, '-') . "\n";
		echo "  ID:               {$userId}\n";
		echo "  Email:            {$user[$this->__email]}\n";
		echo "  Username:         {$user[$this->__username]}\n";
		echo "  Nombre:           " . trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) . "\n";
		echo "  Activo:           {$isActive}\n";
		echo "  Email verificado: {$emailVer}\n";

		$role = DB::table('user_roles')
			->join('roles', 'user_roles.role_id', '=', 'roles.id')
			->where('user_roles.user_id', $userId)
			->value('roles.name');

		echo "  Rol:              " . ($role ?? 'N/A') . "\n";

		if (!empty($user['created_at'])){
			echo "  Creado:           {$user['created_at']}\n";
		}
		if (!empty($user['updated_at'])){
			echo "  Actualizado:      {$user['updated_at']}\n";
		}
	}

	// ==================================================================
	//  test-login
	// ==================================================================
	public function test_login(...$options){
		$opts  = $this->parseOptions($options);
		$email = $opts['email'] ?? null;
		$pass  = $opts['password'] ?? null;

		if (!$email || !$pass){
			echo "Error: --email y --password son requeridos.\n";
			echo "Uso: php com users test-login --email=user@example.com --password=secret123\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$hash = $user[$this->__password] ?? '';

		if (password_verify($pass, $hash)){
			echo "✓ Login exitoso para '{$email}'\n";
			echo "  ID:     {$user[$this->__id]}\n";

			$role = DB::table('user_roles')
				->join('roles', 'user_roles.role_id', '=', 'roles.id')
				->where('user_roles.user_id', $user[$this->__id])
				->value('roles.name');

			echo "  Rol:    " . ($role ?? 'N/A') . "\n";
			echo "  Activo: " . (($user[$this->__isActive] ?? 1) ? '✓' : '✗') . "\n";
		} else {
			echo "✗ Credenciales inválidas para '{$email}'.\n";
		}
	}

	// ==================================================================
	//  update-email
	// ==================================================================
	public function update_email(...$options){
		$opts = $this->parseOptions($options);
		$uid  = $opts['uid'] ?? null;
		$newEmail = $opts['email'] ?? null;

		if (!$uid || !$newEmail){
			echo "Error: --uid y --email son requeridos.\n";
			echo "Uso: php com users update-email --uid=UID --email=nuevo@email.com\n";
			return;
		}

		$user = DB::table($this->usersTable)
			->where([$this->__id => $uid])
			->first();

		if (!$user){
			echo "✗ Usuario con ID '{$uid}' no encontrado.\n";
			return;
		}

		$oldEmail = $user[$this->__email];

		if ($this->getUserByEmail($newEmail)){
			echo "✗ El email '{$newEmail}' ya está en uso.\n";
			return;
		}

		$this->updateUser($uid, [
			$this->__email        => $newEmail,
			$this->__confirmedEmail => 0,
		]);

		echo "✓ Email actualizado de '{$oldEmail}' a '{$newEmail}'.\n";
		echo "  El email deberá verificarse nuevamente.\n";
	}

	// ==================================================================
	//  verify-email
	// ==================================================================
	public function verify_email(...$options){
		[$email] = $this->extractEmail($options);

		if (!$email){
			echo "Error: Debes proporcionar un email.\n";
			echo "Uso: php com users verify-email <email>\n";
			return;
		}

		$user = $this->getUserByEmail($email);

		if (!$user){
			echo "✗ Usuario '{$email}' no encontrado.\n";
			return;
		}

		$this->updateUser($user[$this->__id], [$this->__confirmedEmail => 1]);

		echo "✓ Email '{$email}' marcado como verificado.\n";
	}

	// ==================================================================
	//  assign role helper
	// ==================================================================
	protected function assignRole(int $userId, string $roleName): bool{
		$role = DB::table('roles')->where(['name' => $roleName])->first();

		if (!$role){
			echo "  ⚠ Rol '{$roleName}' no encontrado en la tabla roles.\n";
			return false;
		}

		DB::table('user_roles')->insert([
			'user_id' => $userId,
			'role_id' => $role['id'],
		]);

		return true;
	}

	// ==================================================================
	//  HELP
	// ==================================================================
	function help($name = null, ...$args){
		$str = <<<STR
╔═══════════════════════════════════════════════════════════════════════════════╗
║                          USERS COMMAND HELP                                  ║
║                  Gesti\u{00f3}n de usuarios del framework                      ║
╚═══════════════════════════════════════════════════════════════════════════════╝

\e[1mCOMANDOS DISPONIBLES\e[0m

  \e[1mcreate-user\e[0m     Crea un nuevo usuario
                  \e[2mAliases:\e[0m create, new, add
                  php com users create-user --email=user@example.com --password=secret123
                  php com users create-user --email=admin@example.com --password=secret123 --role=admin --display-name="Admin User"

  \e[1mdelete-user\e[0m     Elimina un usuario existente
                  \e[2mAliases:\e[0m delete, del, rm, remove, del-user, rm-user
                  php com users delete-user user@example.com
                  php com users delete-user user@example.com --force
                  php com users delete-user --email=user@example.com --force

  \e[1mdisable-user\e[0m    Deshabilita una cuenta de usuario
                  php com users disable-user user@example.com
                  php com users disable-user --email=user@example.com

  \e[1menable-user\e[0m     Habilita una cuenta de usuario
                  php com users enable-user user@example.com
                  php com users enable-user --email=user@example.com

  \e[1mlist-users\e[0m      Lista todos los usuarios con sus roles y estado
                  \e[2mAliases:\e[0m ls, list, ls-users
                  php com users list-users
                  php com users list-users --role=admin
                  php com users list-users --disabled-only

  \e[1mset-password\e[0m    Establece una nueva contrase\u{00f1}a para un usuario
                  php com users set-password --email=user@example.com --password=newPassword123

  \e[1mset-role\e[0m        Establece un nuevo rol para un usuario
                  php com users set-role --email=user@example.com --role=admin

  \e[1mshow-user\e[0m       Muestra informaci\u{00f3}n completa de un usuario
                  \e[2mAliases:\e[0m show, get, info
                  php com users show-user user@example.com
                  php com users show-user --email=user@example.com

  \e[1mtest-login\e[0m      Verifica las credenciales de un usuario usando la API REST
                  php com users test-login --email=user@example.com --password=secret123

  \e[1mupdate-email\e[0m    Actualiza el email de un usuario
                  php com users update-email --uid=UID --email=nuevo@email.com

  \e[1mverify-email\e[0m    Marca el email del usuario como verificado
                  php com users verify-email --email=user@example.com

\e[2m---------------------------------------------------------------------\e[0m
\e[1mAyuda espec\u{00ed}fica:\e[0m php com users <comando> --help

STR;

		echo $str;
	}
}
