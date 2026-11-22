# SimpleRest Basic ACL

`boctulus/simplerest-basic-acl` is a minimal concrete ACL for SimpleRest. It loads the role list from the `roles` table and otherwise relies on the role and permission builder of the core `Boctulus\Simplerest\Core\Security\Acl`. Use `boctulus/simplerest-fine-grained-acl` for special, per-user and deny permissions.

## Installation

```bash
composer require boctulus/simplerest-basic-acl
```

1. Register the provider in `config/config.php`:

   ```php
   'providers' => [
       Boctulus\BasicACL\BasicAclServiceProvider::class,
   ],
   ```

2. Create `config/acl.php` that returns the ACL instance (`Factory::acl()` includes it). The roles come from the `roles` table, so do not add them again with `addRole()`; assign permissions to the existing roles with the `$to_role` argument:

   ```php
   <?php

   use Boctulus\BasicACL\Acl;

   $acl = new Acl();

   $acl->setAsGuest('guest')
       ->addResourcePermissions('products', ['read'], 'registered')
       ->addSpecialPermissions(['read_all', 'write_all'], 'admin');

   return $acl;
   ```

   A role without permissions, such as `guest` above, gets HTTP 401 from the generated API.

3. The `roles` table must exist and contain the roles you reference. The base migrations from `vendor/bin/simplerest init` (`boctulus/simplerest-cli`) create and seed it.

## Requirements

PHP `>=8.1,<8.5` and `boctulus/simplerest` `^2.0 || ^3.0`.

## License

MIT.
