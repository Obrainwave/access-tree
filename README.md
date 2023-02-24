# This package is to all access and permission rights in any laravel project

This package is to all access and permission rights in any laravel project


## Installation

You can install the package via composer:

```bash
composer require obrainwave/access-tree
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag=":access-tree-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="access-tree-config"
```

## Usage

Create a Permission
```php
$data = [
     'name' => 'Add User',
     'status' => 1 or 0
   ];
$create = createAccess($data, 'permission');
echo $create;
```

Create a Role
```php
$data = [
     'name' => 'Admin',
     'status' => 1 or 0
   ];
$permission_ids = array of permission ids // array(1, 5, 4);
$create = createAccess($data, 'role', $permission_ids);
echo $create;
```

Create User Role
```php 
$roles = array of roles // array(2, 5);
$user_id = id of a user from laravel User Model // 1;
$user_role = createUserRole($roles, $user_id);
echo $user_role;
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Olaiwola Akeem Salau](https://github.com/Obrainwave)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
