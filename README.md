# Laravel Access Tree

[![Latest Version on Packagist](https://img.shields.io/packagist/v/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![Total Downloads](https://img.shields.io/packagist/dt/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![License](https://img.shields.io/packagist/l/obrainwave/access-tree.svg?style=flat-square)](LICENSE.md)

## 🎉 AccessTree 2.2.0 - Major Release

**Version 1.x is deprecated. Please follow the [Upgrade Guide](UPGRADE.md) before updating.**

### What's New in 2.2?
- 🎨 **Complete Admin Interface**: Modern, responsive admin panel with dark mode
- 🗄️ **Universal Table Management**: Automatically manage ALL database tables with full CRUD
- 📊 **Dynamic Dashboard**: Real-time statistics and recent activity feed
- 🌙 **Dark/Light Mode**: Beautiful UI theme switcher
- 📄 **System Logs Viewer**: View and manage application logs
- ⚙️ **System Settings**: Monitor system health and performance
- 🎭 **Custom Pagination**: Modern, styled pagination with dark mode
- 📱 **Fully Responsive**: Works perfectly on all devices
- 🔥 **Sticky Header**: Always-visible header with breadcrumbs
- ✨ **Smooth Animations**: Professional transitions and hover effects

**And much more!** See the [Features](#features) section below.

---

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

# 4) Install Admin Interface (Optional)
php artisan vendor:publish --tag="accesstree-admin-views"
php artisan vendor:publish --tag="accesstree-admin-routes"
```

Add trait to your User model:
```php
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
}
```

**Access the Admin Panel**: Visit `/admin/accesstree/login` after creating a user.

You're done — roles, permissions and a seeded admin are ready (if seeding enabled).


## Features

### Core Features
- **Role-Based Access Control (RBAC)**: Create and manage roles with specific permissions
- **User Role Assignment**: Assign multiple roles to users with easy synchronization
- **Permission Checking**: Helper functions for checking permissions and roles
- **Root User Bypass**: Special superuser capability that bypasses all permission checks
- **Caching System**: Built-in caching for optimal performance
- **Blade Directives**: Easy-to-use directives for view-level authorization
- **Customisable Seeder**: Seeder with default permissions/roles (publishable for customisation)
- **Flexible API**: Service classes, Facades, and global helper functions

### Admin Interface (New in 2.0+)
- **🖥️ Built-in Admin Dashboard**: Complete admin interface with modern, responsive design
- **🌙 Dark/Light Mode Toggle**: UI theme switcher with localStorage persistence
- **📊 Dynamic Dashboard**: Real-time statistics and recent activity feed
- **🔍 Universal Table Management**: Automatically manage ALL database tables with full CRUD
- **📋 Configurable Table Management**: Control which tables appear in the sidebar and dashboard
- **🎨 Modern UI/UX**: Gradient backgrounds, animations, glassmorphism effects
- **📱 Fully Responsive**: Mobile-friendly design that works on all devices
- **🔐 Secure Authentication**: Built-in login system for admin access
- **📄 System Logs Viewer**: View and manage application logs from the admin panel
- **⚙️ System Settings**: Monitor system information, storage, and performance
- **🎭 Custom Pagination**: Beautiful, modern pagination with dark mode support
- **🔥 Sticky Header**: Fixed header with breadcrumbs and quick actions
- **🎯 Dynamic Sidebar**: Auto-discover and list all database tables
- **✨ Smooth Animations**: Professional transitions and hover effects


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

### Installation Options
The package provides flexible installation options:

```bash
# Basic installation
php artisan accesstree:install

# Install with admin interface
php artisan accesstree:install --with-admin

# Install with Laravel Gates integration
php artisan accesstree:install --with-gates

# Install with both admin interface and gates
php artisan accesstree:install --with-admin --with-gates

# Force reinstall (overwrites existing files)
php artisan accesstree:install --force

# Install with all features
php artisan accesstree:install --with-admin --with-gates --force
```


## Configuration

### Publish Config File
```bash
php artisan vendor:publish --tag="accesstree-config"
```

### Config File Options
After publishing the config file `(config/accesstree.php)`, you can customize the package behavior:
```php
return [

    // Basic Configuration
    'seed_permissions'            => true,
    'seed_roles'                  => true,
    'assign_first_user_as_admin'  => true,
    'cache_refresh_time'          => 5,
    'forbidden_redirect'          => 'home',

    // User Model Configuration
    'user_model'                  => 'App\\Models\\User',

    // Admin Interface Configuration
    'admin_favicon'               => null, // Path to custom favicon

    // Universal Table Management
    'managed_tables'              => [], // Empty = all tables, or specify: ['posts', 'products', 'orders']
    'dashboard_table_cards'       => [], // Which managed tables show cards on dashboard

    // Styling Configuration
    'styling'                     => [
        'framework'  => 'bootstrap', // bootstrap, tailwind, or custom
        'theme'      => 'modern',    // modern, classic, or minimal
        'dark_mode'  => false,       // Enable dark mode by default
        'animations' => true,        // Enable animations
        'custom_css' => null,        // Custom CSS string
    ],
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


## Admin Interface

### Getting Started with Admin Interface

The AccessTree package includes a powerful built-in admin interface for managing permissions, roles, users, and all your database tables.

#### Installation
```bash
# Publish admin views and routes
php artisan vendor:publish --tag="accesstree-admin-views"
php artisan vendor:publish --tag="accesstree-admin-routes"
```

#### Access the Admin Panel
1. Visit `/admin/accesstree/login`
2. Login with your user credentials
3. Access the full admin dashboard

#### Features

##### Dashboard
- **Real-time Statistics**: View total permissions, roles, and users
- **Recent Activity**: See the latest permissions, roles, and users created
- **Managed Tables Overview**: Cards showing managed database tables with record counts
- **System Status**: Monitor system health and performance

##### Permission Management
- Create, view, edit, and delete permissions
- Search and filter permissions
- Paginated list view

##### Role Management
- Create, view, edit, and delete roles
- Assign permissions to roles (multi-select with 3-column layout)
- Manage role-permission relationships

##### User Management
- View all users
- Assign roles to users
- Manage user-role relationships

##### Universal Table Management (New!)
Automatically manage **ALL** database tables with full CRUD operations:

```php
// Configure which tables to manage (in config/accesstree.php)
'managed_tables' => [
    'posts',
    'products',
    'orders',
    'categories'
], // Empty array = manage all tables
```

**Features:**
- **Auto-discovery**: Automatically lists all your database tables in the sidebar
- **Full CRUD**: Create, Read, Update, Delete for any table
- **Smart Field Detection**: Automatically detects field types and renders appropriate inputs
- **Search & Filter**: Search across table records
- **Pagination**: Modern, responsive pagination
- **Bulk Actions**: Delete records in bulk

##### System Pages
- **System Settings**: View PHP version, Laravel version, environment info, database/cache drivers, storage usage, database size
- **System Logs**: View, refresh, download, and clear application logs in real-time

##### UI Features
- **Dark/Light Mode**: Toggle between themes with localStorage persistence
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Sticky Header**: Always-visible header with breadcrumbs
- **Modern Sidebar**: Gradient background with smooth animations
- **Custom Pagination**: Beautiful pagination with dark mode support
- **Dynamic Favicon**: Auto-generated favicon based on app name

### Configuring Universal Table Management

By default, the admin interface manages ALL database tables. You can configure this:

```php
// config/accesstree.php
'managed_tables' => [
    'posts',
    'products', 
    'orders',
    'user_wallet'
], // Only these tables will appear in sidebar
// Leave empty [] to manage all tables
```

**Dashboard Table Cards:**
```php
// config/accesstree.php
'dashboard_table_cards' => [
    'posts',
    'products'
], // Only these will show as cards on dashboard
// Leave empty [] for no table cards
```

### Customization

#### Custom Favicon
```php
// config/accesstree.php
'admin_favicon' => 'images/favicon.ico',
```

#### Custom Styling
```php
// config/accesstree.php
'styling' => [
    'framework'  => 'bootstrap', // bootstrap, tailwind, or custom
    'theme'      => 'modern',    // modern, classic, or minimal
    'dark_mode'  => false,       // Default dark mode
    'animations' => true,        // Enable animations
    'custom_css' => null,        // Custom CSS string
    'custom_js'  => null,        // Custom JavaScript string
],
```

#### Custom CSS Examples

**Simple Branding (.env file):**
```env
ACCESSTREE_CUSTOM_CSS=".btn-primary { background-color: #ff6b6b !important; }"
```

**Complex Styling (config file):**
```php
'custom_css' => '
    /* Import Google Fonts */
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");
    
    /* Brand Colors */
    :root {
        --primary-color: #ff6b6b;
        --secondary-color: #4ecdc4;
        --accent-color: #45b7d1;
    }
    
    /* Custom Logo */
    .admin-logo {
        content: url("/images/company-logo.png");
        width: 120px;
        height: auto;
    }
    
    /* Custom Sidebar */
    .sidebar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    
    /* Custom Buttons */
    .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background-color: #ff5252 !important;
        border-color: #ff5252 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    }
    
    /* Custom Cards */
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    /* Custom Form Controls */
    .form-control {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
    }
    
    /* Dark Mode Overrides */
    body.dark-mode .sidebar {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }
    
    body.dark-mode .card {
        background: #2d3748;
        border-color: #4a5568;
    }
',
```

#### Custom JavaScript Examples

**Simple Analytics (.env file):**
```env
ACCESSTREE_CUSTOM_JS="gtag('config', 'GA_MEASUREMENT_ID');"
```

**Advanced Functionality (config file):**
```php
'custom_js' => '
    // Google Analytics
    gtag("config", "GA_MEASUREMENT_ID");
    
    // Toastr Configuration
    toastr.options = {
        positionClass: "toast-top-right",
        timeOut: 3000,
        progressBar: true,
        closeButton: true
    };
    
    // Custom Form Enhancements
    $(document).ready(function() {
        // Add focus effects to form controls
        $(".form-control").on("focus", function() {
            $(this).parent().addClass("focused");
        }).on("blur", function() {
            $(this).parent().removeClass("focused");
        });
        
        // Custom tooltips
        $("[data-toggle=\"tooltip\"]").tooltip();
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 5000);
        
        // Real-time notifications
        if (typeof(EventSource) !== "undefined") {
            var source = new EventSource("/admin/notifications/stream");
            source.onmessage = function(event) {
                var data = JSON.parse(event.data);
                toastr.success(data.message);
            };
        }
        
        // Custom loading states
        $("form").on("submit", function() {
            var submitBtn = $(this).find("button[type=\"submit\"]");
            submitBtn.prop("disabled", true).html("<i class=\"fas fa-spinner fa-spin\"></i> Processing...");
        });
    });
    
    // Custom utility functions
    function showCustomModal(title, content) {
        $("#customModal .modal-title").text(title);
        $("#customModal .modal-body").html(content);
        $("#customModal").modal("show");
    }
    
    function refreshDashboard() {
        fetch("/admin/dashboard/data")
            .then(response => response.json())
            .then(data => {
                $("#total-users").text(data.users);
                $("#total-roles").text(data.roles);
                $("#total-permissions").text(data.permissions);
            });
    }
    
    // Auto-refresh every 30 seconds
    setInterval(refreshDashboard, 30000);
',
```

#### Environment Variables (.env)
```env
# CSS Framework
ACCESSTREE_CSS_FRAMEWORK=bootstrap

# Theme
ACCESSTREE_THEME=modern

# Dark Mode
ACCESSTREE_DARK_MODE=false

# Animations
ACCESSTREE_ANIMATIONS=true

# Custom CSS (simple)
ACCESSTREE_CUSTOM_CSS=".btn-primary { background-color: #ff6b6b !important; }"

# Custom JavaScript (simple)
ACCESSTREE_CUSTOM_JS="gtag('config', 'GA_MEASUREMENT_ID');"
```

### Accessing Admin Programmatically

The admin routes are automatically registered. You can customize them by publishing and editing `routes/accesstree-admin.php`.

**Default Route Prefix**: `/admin/accesstree`


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

# Publish admin interface
php artisan vendor:publish --tag=accesstree-admin-views
php artisan vendor:publish --tag=accesstree-admin-routes
php artisan vendor:publish --tag=accesstree-modern-css

# Debug and testing commands
php artisan accesstree:test-route-access {table} {id}
php artisan accesstree:test-universal-routes
php artisan accesstree:test-actual-route {table} {id}
php artisan accesstree:test-route-with-auth {table} {id}

# User model configuration
php artisan accesstree:configure-user-model
php artisan accesstree:test-user-model

# Table discovery and management
php artisan accesstree:discover-tables
php artisan accesstree:setup-universal-admin

# Styling and UI
php artisan accesstree:configure-styling
php artisan accesstree:publish-modern-css

# Testing and debugging
php artisan accesstree:test-access-tree
php artisan accesstree:test-views
php artisan accesstree:test-dynamic-sidebar
php artisan accesstree:test-table-routes
php artisan accesstree:check-routes
php artisan accesstree:debug-admin-routes

# Admin user management
php artisan accesstree:create-admin-user

# Cleanup and maintenance
php artisan accesstree:cleanup-test-data
php artisan accesstree:install-admin-interface
php artisan accesstree:install
php artisan accesstree:install --with-admin
php artisan accesstree:install --with-gates
php artisan accesstree:install --with-admin --with-gates
php artisan accesstree:install --force

# Clear package caches
php artisan accesstree:clear-cache
```

### Command Descriptions

#### Core Commands
- `accesstree:seed` - Seed default permissions and roles
- `accesstree:install` - Complete package installation
- `accesstree:install --with-admin` - Install with admin interface
- `accesstree:install --with-gates` - Install with Laravel Gates integration
- `accesstree:install --with-admin --with-gates` - Install with both admin interface and gates
- `accesstree:install --force` - Force reinstall (overwrites existing files)
- `accesstree:install-admin-interface` - Install admin interface components

#### User Management
- `accesstree:create-admin-user` - Create an admin user for the interface
- `accesstree:configure-user-model` - Configure the User model for your application
- `accesstree:test-user-model` - Test if the User model is properly configured

#### Table Management
- `accesstree:discover-tables` - Discover all database tables in your application
- `accesstree:setup-universal-admin` - Set up universal table management

#### Styling & UI
- `accesstree:configure-styling` - Configure CSS framework and theme options
- `accesstree:publish-modern-css` - Publish modern dashboard CSS

#### Testing & Debugging
- `accesstree:test-access-tree` - Test core AccessTree functionality
- `accesstree:test-views` - Test if admin views are properly loaded
- `accesstree:test-dynamic-sidebar` - Test dynamic sidebar functionality
- `accesstree:test-table-routes` - Test universal table routes
- `accesstree:test-route-access {table} {id}` - Test access to specific table records
- `accesstree:test-universal-routes` - Test universal table route generation
- `accesstree:test-actual-route {table} {id}` - Test actual route execution
- `accesstree:test-route-with-auth {table} {id}` - Test routes with authentication
- `accesstree:check-routes` - Check if all admin routes are properly registered
- `accesstree:debug-admin-routes` - Debug admin route issues

#### Maintenance
- `accesstree:cleanup-test-data` - Clean up test data created during testing
- `accesstree:clear-cache` - Clear all AccessTree-related caches

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

### ✅ Completed (v2.2+)
- ✅ Admin UI with modern design
- ✅ Dark/Light mode toggle
- ✅ Universal table management
- ✅ System logs viewer
- ✅ System settings page
- ✅ Custom pagination
- ✅ Responsive design
- ✅ Dynamic sidebar
- ✅ Dashboard with statistics and recent activity
- ✅ Sticky header with breadcrumbs
- ✅ Artisan command to clear package caches

### 🔜 Planned Improvements
* Livewire / Inertia / Filament integration examples
* Per-tenant role scoping (multitenancy)
* Advanced filtering and sorting for universal tables
* Bulk operations for multiple tables
* Export/Import functionality for table data
* Custom field validation rules
* API endpoints for admin operations
* Audit logging for permission/role changes

If you want any roadmap item prioritized — open an issue or PR.


## Acknowledgements
Inspired by common RBAC patterns (and packages like spatie/laravel-permission). Access Tree focuses on lightweight, configurable seeding and an intuitive helper API.

