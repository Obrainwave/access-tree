# AccessTree Enhanced - Installation Guide

## Quick Installation

### 1. Install the Package
```bash
composer require obrainwave/access-tree
```

### 2. Run the Enhanced Installation
```bash
# Install with admin interface and Gates integration
php artisan accesstree:install --with-admin --with-gates
```

### 3. Configure User Model
```bash
# Auto-detect and configure User model
php artisan accesstree:configure-user-model --auto-detect

# Or specify manually
php artisan accesstree:configure-user-model --model="App\\Models\\User"
```

### 4. Add Trait to User Model
```php
// app/Models/User.php
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
    
    // ... rest of your code
}
```

### 5. Create Admin User
```bash
# Create the first admin user
php artisan accesstree:create-admin --email=admin@example.com --password=password
```

### 5. Clear Caches (if needed)
```bash
# Clear all caches and rebuild autoloader
php artisan accesstree:clear-cache
```

### 6. Test the Installation
```bash
# Test that everything is working
php artisan accesstree:test
```

## Manual Installation (Step by Step)

If you prefer to install manually:

### 1. Publish and Run Migrations
```bash
php artisan vendor:publish --tag="accesstree-migrations"
php artisan migrate
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --tag="accesstree-config"
```

### 3. Seed Default Data
```bash
php artisan accesstree:seed
```

### 4. Install Admin Interface (Optional)
```bash
php artisan accesstree:install-admin
```

## Access the Admin Interface

Once installed, visit `/admin/accesstree` to access the admin panel.

### Login Credentials
- **URL**: `/admin/accesstree/login`
- **Email**: Use the email you provided when creating the admin user
- **Password**: Use the password you provided when creating the admin user

### First Time Setup
1. Visit `/admin/accesstree/login`
2. Login with your admin credentials
3. You'll be redirected to the dashboard
4. Start managing permissions, roles, and users!

## Troubleshooting

### Common Issues

1. **"No hint path defined for [accesstree]"**: Run `php artisan accesstree:clear-cache`
2. **"This cache store does not support tagging"**: This is normal for file/database cache drivers. The package will work fine, just without advanced cache tagging.
3. **Class not found errors**: Run `composer dump-autoload` or `php artisan accesstree:clear-cache`
4. **Migration errors**: Check that your database is properly configured
5. **Permission errors**: Make sure you've added the `HasRole` trait to your User model
6. **View not found errors**: Clear view cache with `php artisan view:clear`

### Testing Commands

```bash
# Test the package functionality
php artisan accesstree:test

# Debug admin routes and views
php artisan accesstree:debug-routes

# Check if admin interface is working
php artisan route:list | grep accesstree
```

### Configuration

The package configuration is in `config/accesstree.php`. Key settings:

- `admin_interface.enabled` - Enable/disable admin interface
- `cache_refresh_time` - Cache duration in minutes
- `cache_driver` - Cache driver to use (default: same as Laravel's default)
- `gates.enabled` - Enable Laravel Gates integration

### Cache Driver Optimization

For better performance, consider using a cache driver that supports tagging:

```php
// config/cache.php - Set default cache driver
'default' => env('CACHE_DRIVER', 'redis'),

// Or configure AccessTree specifically
// config/accesstree.php
'cache_driver' => 'redis', // or 'memcached'
```

**Supported cache drivers:**
- ✅ **Redis** - Best performance, supports tagging
- ✅ **Memcached** - Good performance, supports tagging  
- ✅ **File** - Basic performance, no tagging (works fine)
- ✅ **Database** - Basic performance, no tagging (works fine)

## Next Steps

1. **Create your first admin user**: Assign roles to users through the admin interface
2. **Customize permissions**: Add your application-specific permissions
3. **Use in your application**: Use the helper functions or service methods
4. **API integration**: Use the RESTful API endpoints for frontend applications

## Troubleshooting

### User Model Issues

**Error: `Class "Obrainwave\AccessTree\Models\User" not found`**

This error occurs when the package cannot find your User model. Here's how to fix it:

```bash
# 1. Auto-detect your User model
php artisan accesstree:configure-user-model --auto-detect

# 2. Or specify it manually
php artisan accesstree:configure-user-model --model="App\\Models\\User"

# 3. Or set it in your .env file
echo "ACCESSTREE_USER_MODEL=App\\Models\\User" >> .env
```

**Common User model locations:**
- `App\Models\User` (Laravel 8+)
- `App\User` (Laravel 7 and below)
- `Illuminate\Foundation\Auth\User` (Default Laravel)

## Support

If you encounter any issues:
1. Check the logs in `storage/logs/laravel.log`
2. Run `php artisan accesstree:test` to verify functionality
3. Check the configuration in `config/accesstree.php`
4. Run `php artisan accesstree:configure-user-model --auto-detect` for User model issues
