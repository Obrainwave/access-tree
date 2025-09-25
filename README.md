# Laravel Access Tree

[![Latest Version on Packagist](https://img.shields.io/packagist/v/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![Total Downloads](https://img.shields.io/packagist/dt/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![License](https://img.shields.io/packagist/l/obrainwave/access-tree.svg?style=flat-square)](LICENSE.md)

AccessTree 2.0.0 is a major release. Version 1.x is deprecated.  
Please follow the [Upgrade Guide](UPGRADE.md) before updating.

A comprehensive Laravel package for managing user permissions and roles with database-driven access control.

This package is a lightweight, database-driven RBAC (roles & permissions) package for Laravel with convenient helpers, facades, caching, seeding and middleware.


## Quick Start (2 minutes)
```bash
# 1) Require package
composer require obrainwave/access-tree

# 2) Publish migrations & config
php artisan vendor:publish --tag="accesstree-migrations"
php artisan vendor:publish --tag="accesstree-config"

# 3) Migrate & seed (optional)
php artisan migrate
php artisan accesstree:seed

```

Add trait to your User model:
```php
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
}
```
You're done — roles, permissions and a seeded admin are ready (if seeding enabled).


## Features

- **Role-Based Access Control (RBAC)**: Create and manage roles with specific permissions
- **User Role Assignment**: Assign multiple roles to users with easy synchronization
- **Permission Checking**: Helper functions for checking permissions and roles
- **Root User Bypass**: Special superuser capability that bypasses all permission checks
- **Caching System**: Built-in caching for optimal performance
- **Blade Directives**: Easy-to-use directives for view-level authorization
- **Customisable Seeder**: Seeder with default permissions/roles (publishable for customisation)
- **Flexible API**: Service classes, Facades, and global helper functions


## Requirements & Compatibility
* __PHP:__ 8.1+
* __Laravel:__ 8.x, 9.x, 10.x, 11.x, 12.x
* The package is continuously tested on the latest stable Laravel versions.
* Please open an issue


## Installation

### 1. Require via Composer
```bash
composer require obrainwave/access-tree
```

### Publish and Run Migrations
```bash
php artisan vendor:publish --tag="accesstree-migrations"
php artisan migrate
```
After running migration, a new column `is_root_user` will be added to the users table.


## Configuration

### Publish Config File
```bash
php artisan vendor:publish --tag="accesstree-config"
```

### Config File Options
After publishing the config file `(config/accesstree.php)`, you can customize the package behavior:
```php
return [

    'seed_permissions'            => true,

    'seed_roles'                  => true,

    'assign_first_user_as_admin'  => true,

    'cache_refresh_time' => 5,

    'forbidden_redirect' => 'home',
];
```

### Publish Seeder (Optional)
```bash
php artisan vendor:publish --tag="accesstree-seeders"
```

### Seeding Features
The package includes a powerful seeding system that can automatically set up your permissions and roles:

#### Default Permissions Created:
* User Management: `view user`, `add user`, `edit user`, `delete user`
* Role Management: `view role`, `add role`, `edit role`, `delete role`
* Permission Management: `view permission`, `add permission`, `edit permission`, `delete permission`
* Content Management: Common CRUD operations for your application

### Default Roles Created:
* Super Admin: Has all permissions
* Admin: Has all permissions except permission control
* Editor: Can create, read, and update content

### Seeding
After publishing the seeder, you can customize it at `database/seeders/PermissionSeeder.php` and run:
```bash
 php artisan accesstree:seed
```

### Automatic First User as Admin
If enabled in the config file:
```php
// config/accesstree.php
'assign_first_user_as_admin' => true,
```
The package will automatically assign the very first registered user the Admin role (if it exists).

* Ensures you always have at least one administrator.
* Works only on the first user (ID=1 typically).
* If you don’t want this behavior, simply set:
```php
'assign_first_user_as_admin' => false,
```

### Add Trait to User Model
```php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
}
```


## Basic Usage

### Creating Permissions and Roles

#### Using service class or the service facade 

```php
use Obrainwave\AccessTree\Facades\AccessTree; or use AccessTree;

// Create permission
AccessTree::createAccess([
    'name' => 'Edit Articles',
    'status' => 1
], 'Permission');

// Create role with permissions
AccessTree::createAccess([
    'name' => 'Editor',
    'status' => 1
], 'Role', [1, 2, 3]); // Permission IDs
```

#### Using Helper Functions

```php
// Create permission
createAccess(['name' => 'Delete Articles'], 'Permission');

// Create role
createAccess(['name' => 'Admin'], 'Role', [1, 2, 3, 4]);
```

### Managing User Roles
```php
$user = User::find(1);

// Assign a single role
$user->assignRole('admin');

// Assign multiple roles
$user->assignRoles(['editor', 'moderator']);

// Sync roles (removes existing, adds new ones)
$user->syncRoles([1, 2, 3]);

// Using helper function
createUserRole([1, 2], $user->id);
```

### Checking Permissions and Roles
```php
// Check single permission
if (checkPermission('edit_articles')) {
    // User has permission
}

// Check multiple permissions (strict - all required)
if (checkPermissions(['edit_articles', 'delete_articles'], true)) {
    // User has ALL permissions
}

// Check multiple permissions (any - at least one)
if (checkPermissions(['edit_articles', 'delete_articles'], false)) {
    // User has AT LEAST ONE permission
}

// Check role
if (checkRole('admin')) {
    // User has role
}

// Using model methods
if ($user->hasRole('admin')) {
    // User is admin
}

if ($user->hasPermission('edit_articles')) {
    // User can edit articles
}
```

### Blade Directives
```php
@if(checkPermission('edit_articles'))
    <button>Edit Article</button>
@endif

@if(checkRole('admin'))
    <div class="admin-panel">Admin Controls</div>
@endif

@if(checkPermissions(['edit_articles', 'delete_articles'], true))
    <div>User can both edit and delete articles</div>
@endif
```

### Root User Feature
```php
// Mark user as root
$user = User::find(1);
$user->is_root_user = true;
$user->save();

// Check if current user is root
if (isRootUser()) {
    // Bypasses all permission checks
}

// Check if specific user is root
if (isRootUser($user->id)) {
    // User has full access
}
```


## Middleware Usage
Protect your routes:
```php
// Single permission
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('accesstree:add_permission');

// Multiple permissions (any)
Route::post('/articles', [ArticleController::class, 'store'])
    ->middleware('accesstree:create_articles,edit_articles');

// Role check
Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])
    ->middleware('accesstree:role:admin');
```
Middleware supports `role:slug` prefix to explicitly request role checks. It returns JSON 403 for XHR or redirects back/route with `danger` flash message.


## Artisan Commands
### Available Commands
```bash
# Seed default permissions and roles
php artisan accesstree:seed

# Fresh seed (WARNING: clears all access-tree tables before reseeding!)
php artisan accesstree:seed --fresh

# Publish package assets
php artisan vendor:publish --tag=accesstree-config
php artisan vendor:publish --tag=accesstree-migrations
php artisan vendor:publish --tag=accesstree-seeders
```

### Warning:
Unlike `php artisan migrate:fresh`, this command does not wipe your entire database.
It will only truncate AccessTree-related tables defined in `config/accesstree.php` (by default):

* `permissions`
* `roles`
* `role_has_permissions`
* `user_roles`
This ensures your application data remains safe while reseeding roles & permissions.


## Advanced Usage
### Using Service Directly
```php
use Obrainwave\AccessTree\Services\AccessTreeService;

$accessService = new AccessTreeService();

// Update role
$result = $accessService->updateAccess([
    'data_id' => 1,
    'name' => 'Senior Editor',
    'status' => 1
], 'Role', [1, 2, 3, 4, 5]);

// Delete permission
$result = $accessService->deleteAccess(1, 'Permission');
```

### Data Retrieval
```php
// Get all active permissions
$permissions = fetchActivePermissions();

// Get roles with pagination
$roles = fetchRoles([
    'paginate' => true,
    'per_page' => 15,
    'with_relation' => true,
    'order' => 'asc',
    'order_ref' => 'name'
]);

// Get specific role with permissions
$role = fetchRole(1);

// Get user's roles
$userRoles = fetchUserRoles($user->id);

// Get role permissions
$rolePermissions = fetchRolePermissions($roleId);
```


## Available Methods
### Service Class Methods
* `createAccess(array $data, string $model, array $permission_ids = [])`
* `updateAccess(array $data, string $model, array $permission_ids = [])`
* `deleteAccess(int $data_id, string $model)`

### HasRole Trait Methods
* `roles()` - Relationship to user's roles
* `assignRole($role)` - Assign single role
* `assignRoles($roles)` - Assign multiple roles
* `syncRoles($roles)` - Sync user roles
* `hasRole($role)` - Check if user has role
* `permissions()` - Get user's permissions
* `hasPermission($permission)` - Check if user has permission

### Helper Functions
* `checkPermission(string $permission): bool`
* `checkPermissions(array $permissions, bool $strict = false): bool`
* `checkRole(string $role): bool`
* `checkRoles(array $roles, bool $strict = false): bool`
* `isRootUser(int $user_id = null): bool`
* `createAccess(array $data, string $model, array $permission_ids = []): string`
* `updateAccess(array $data, string $model, array $permission_ids = []): string`
* `fetchPermissions(array $options = [], int $status = 0): object`
* `fetchRoles(array $options = [], int $status = 0): object`
* `fetchUserRoles(int $user_id, bool $with_relation = true): object`


## Database Schema
The package creates these tables:
* `permissions` - Stores permission records
* `roles` - Stores role records
* `role_has_permissions` - Role-permission relationships
* `user_roles` - User-role relationships


## Caching & Performance
* Permission and role checks are cached per-user for `cache_refresh_time` (config).
* Use `config('accesstree.cache_refresh_time')` to control minutes.
* If you change permissions/role assignments programmatically, make sure to clear/refresh cache for affected users if necessary.


## Testing
Quick example using Laravel feature testing:
```php
public function test_admin_can_access_dashboard()
{
    $user = User::factory()->create();
    AccessTree::assignRoles($user->id, ['Admin']); // or assign role id

    $this->actingAs($user)
         ->get('/admin')
         ->assertStatus(200);
}
```
Seeders can be run in tests to prepare default roles/permissions.


## API Responses & AJAX (example)
When using the package with AJAX (e.g., to modify roles/permissions), the package returns JSON from service controllers (if you build them) and helper functions can be adapted to return structured responses (status/message). Keep errors user-friendly in production, and enable dev logging in local env.


## Security
If you discover any security issues, please email the maintainers at (<olaiwolaakeem@gmail.com>) instead of using the issue tracker.


## License
The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.


## Contributing
We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for coding standards, testing instructions, and local development setup.


## Roadmap
Planned improvements:
* Admin UI scaffolding (optional)
* Livewire / Inertia / Filament integration examples
* Per-tenant role scoping (multitenancy)
* Artisan command to clear package caches

If you want any roadmap item prioritized — open an issue or PR.


## Acknowledgements
Inspired by common RBAC patterns (and packages like spatie/laravel-permission). Access Tree focuses on lightweight, configurable seeding and an intuitive helper API.

