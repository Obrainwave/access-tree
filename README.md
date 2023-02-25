# This package is to all access and permission rights in any laravel project

This package is to all access and permission rights in any laravel project


## Installation

You can install the package via composer:

```bash
composer require obrainwave/access-tree
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="accesstree-migrations"
php artisan migrate
```

Create a Root User:

After running migration, a new column `is_root_user` will be added to the `users` table. To create a user that can override all permissions and roles in your application, set the column to be `true` for the particular user.

You can publish the config file with:

```bash
php artisan vendor:publish --tag="accesstree-config"
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

Update a Permission
```php
$data = [
     'data_id' => permission id from Obrainwave\AccessTree\Models\Permission // 3,
     'name' => 'Add User',
     'status' => 1 or 0
   ];
$update = updateAccess($data, 'permission');
echo $update;
```

Create Roles
```php
$data = [
     'name' => 'Admin',
     'status' => 1 or 0
   ];
$permission_ids = array of permission ids from Obrainwave\AccessTree\Models\Permission // array(1, 5, 4);
$create = createAccess($data, 'role', $permission_ids);
echo $create;
```

Update Roles
```php
$data = [
     'data_id' => role id // 5,
     'name' => 'Admin Staff',
     'status' => 1 or 0
   ];
$permission_ids = array of permission ids from Obrainwave\AccessTree\Models\Permission // array(10, 6, 3);
$update = updateAccess($data, 'role', $permission_ids);
echo $update;
```

Create User Role
```php 
$roles = array of roles from Obrainwave\AccessTree\Models\Role // array(2, 5);
$user_id = id of a user from App\Models\User // 1;
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
