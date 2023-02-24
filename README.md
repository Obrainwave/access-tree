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

#Create a Permission or Role
```php
$data = [
    'data_id' => 5,
     'name' => 'See Auth',
     'status' => 1
   ];
$create = createAccess($data, 'permission');
echo $create;
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
