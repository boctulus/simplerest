<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UserDenyPermissionsSchema implements ISchema
{
    static function get(){
        return [
            'table_name'        => 'user_deny_permissions',

            'id_name'           => 'id',

            'fields'            => ['id', 'user_id', 'resource', 'action', 'created_by', 'created_at', 'updated_by', 'updated_at'],

            'attr_types'        => [
                'id'         => 'INT',
                'user_id'    => 'INT',
                'resource'   => 'STR',
                'action'     => 'STR',
                'created_by' => 'INT',
                'created_at' => 'STR',
                'updated_by' => 'INT',
                'updated_at' => 'STR',
            ],

            'attr_type_detail'  => [],

            'primary'           => ['id'],

            'autoincrement'     => 'id',

            'nullable'          => ['id', 'created_by', 'created_at', 'updated_by', 'updated_at'],

            'required'          => ['user_id', 'resource', 'action'],

            'uniques'           => [
                ['user_id', 'resource', 'action'],
            ],

            'rules'             => [
                'id'         => ['type' => 'int'],
                'user_id'    => ['type' => 'int', 'required' => true],
                'resource'   => ['type' => 'str', 'max' => 100, 'required' => true],
                'action'     => ['type' => 'str', 'max' => 50, 'required' => true],
                'created_by' => ['type' => 'int'],
                'created_at' => ['type' => 'datetime'],
                'updated_by' => ['type' => 'int'],
                'updated_at' => ['type' => 'datetime'],
            ],

            'fks'               => ['user_id'],

            'relationships' => [
                'users' => [
                    ['users.id', 'user_deny_permissions.user_id']
                ],
            ],

            'expanded_relationships' => [
                'users' => [
                    [
                        ['users', 'id'],
                        ['user_deny_permissions', 'user_id'],
                    ],
                ],
            ],

            'relationships_from' => [
                'users' => [
                    ['users.id', 'user_deny_permissions.user_id']
                ],
            ],

            'expanded_relationships_from' => [
                'users' => [
                    [
                        ['users', 'id'],
                        ['user_deny_permissions', 'user_id'],
                    ],
                ],
            ],
        ];
    }
}
