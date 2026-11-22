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

2. Create `config/acl.php` that returns the ACL instance (`Factory::acl()` includes it):

   ```php
   <?php

   use Boctulus\BasicACL\Acl;

   $acl = new Acl();

   $acl->addRole('guest', 1)
       ->setAsGuest('guest')
       ->addRole('registered', 2)
       ->addRole('admin', 1000)
       ->addInherit('registered')
       ->addSpecialPermissions(['read_all', 'write_all']);

   return $acl;
   ```

3. The `roles` table must exist. The base migrations from `vendor/bin/simplerest init` (`boctulus/simplerest-cli`) create it.

## Requirements

PHP `>=8.1,<8.5` and `boctulus/simplerest` `^2.0 || ^3.0`.

## License

MIT.
