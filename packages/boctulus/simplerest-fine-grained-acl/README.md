# SimpleRest Fine-Grained ACL

`boctulus/simplerest-fine-grained-acl` provides a concrete ACL for SimpleRest: roles, role inheritance, special permissions (`read_all`, `write_all`, …), per-table permissions, per-user table and special permissions, and deny rules. The SimpleRest core only declares the abstract `Boctulus\Simplerest\Core\Security\Acl`; API requests need a concrete implementation such as this one.

## Installation

```bash
composer require boctulus/simplerest-fine-grained-acl
```

1. Register the provider in `config/config.php`:

   ```php
   'providers' => [
       Boctulus\FineGrainedACL\FineGrainedAclServiceProvider::class,
   ],
   ```

2. Create `config/acl.php` that returns the ACL instance. `Factory::acl()` includes this file. A project created with `vendor/bin/simplerest init` (`boctulus/simplerest-cli`) already has `config/acl.php.example` for this package:

   ```bash
   cp config/acl.php.example config/acl.php
   ```

3. Run the base migrations from `simplerest init`: `roles`, `sp_permissions`, `user_roles`, `user_tb_permissions`, `user_sp_permissions`, `user_deny_permissions`. The ACL and the core security layer read roles, permissions and deny rules from these tables through the default database connection.

## Requirements

PHP `>=8.1,<8.5` and `boctulus/simplerest` `^2.0 || ^3.0`.

## License

MIT.
