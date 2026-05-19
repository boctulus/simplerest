<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseUsersCommand.php';

class CreateUserCommand extends BaseUsersCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'create-user';
        $this->description = 'Crea un nuevo usuario';
        $this->aliases     = ['create', 'new', 'add'];
        $this->examples    = [
            'php com users create-user --email=user@example.com --password=secret123',
            'php com users create-user --email=admin@example.com --password=secret123 --role=admin',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['email', 'password'],
            'optional' => ['username', 'firstname', 'lastname', 'display-name', 'role'],
            'flags'    => [],
            'options'  => [
                'email'        => ['describe' => 'Email del usuario'],
                'password'     => ['describe' => 'Contraseña'],
                'username'     => ['describe' => 'Nombre de usuario (default: parte del email)'],
                'firstname'    => ['describe' => 'Nombre'],
                'lastname'     => ['describe' => 'Apellido'],
                'display-name' => ['describe' => 'Nombre para mostrar'],
                'role'         => ['describe' => 'Rol a asignar'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $email       = $this->opt($parsed, 'email');
        $password    = $this->opt($parsed, 'password');
        $username    = $this->opt($parsed, 'username') ?? explode('@', $email)[0];
        $firstname   = $this->opt($parsed, 'firstname') ?? $this->opt($parsed, 'display_name') ?? $username;
        $lastname    = $this->opt($parsed, 'lastname', '');
        $role        = $this->opt($parsed, 'role');

        if ($this->getUserByEmail($email)) {
            echo "✗ El email '{$email}' ya está registrado.\n";
            return;
        }

        $data = [
            $this->emailField     => $email,
            $this->passwordField  => $password,
            $this->usernameField  => $username,
            'firstname'           => $firstname,
            'lastname'            => $lastname,
            $this->isActiveField  => 1,
            $this->confirmedField => 0,
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        $id = DB::table($this->usersTable)->create($data);

        if ($id) {
            echo "✓ Usuario creado exitosamente\n";
            echo "  ID:       {$id}\n";
            echo "  Email:    {$email}\n";
            echo "  Username: {$username}\n";
            if ($role) {
                $this->assignRole($id, $role);
                echo "  Rol:      {$role}\n";
            }
        } else {
            echo "✗ Error al crear usuario.\n";
        }
    }
}
