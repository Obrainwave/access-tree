# Laravel AccessTree - Enhanced Edition

[![Latest Version on Packagist](https://img.shields.io/packagist/v/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![Total Downloads](https://img.shields.io/packagist/dt/obrainwave/access-tree.svg?style=flat-square)](https://packagist.org/packages/obrainwave/access-tree)
[![License](https://img.shields.io/packagist/l/obrainwave/access-tree.svg?style=flat-square)](LICENSE.md)

AccessTree 2.0.0+ is a **major enhanced release** with a complete rewrite featuring modern Laravel patterns, zero-configuration admin interface, and enterprise-grade features.

## 🚀 What's New in Enhanced Edition

### ✨ Zero-Configuration Admin Interface
- **Complete CRUD interface** for permissions, roles, and users
- **Auto-generated forms** and tables
- **Built-in search and filtering**
- **Responsive design** with Bootstrap 5
- **Permission-aware** - automatically respects user permissions
- **No coding required** - works out of the box

### 🏗️ Modern Architecture
- **Repository Pattern** for clean data access
- **Service Layer** with dependency injection
- **Response Objects** for consistent API responses
- **Interface-based design** for testability
- **Advanced caching** with performance optimization

### 🔐 Laravel Gates Integration
- **Automatic gate registration** for seamless authorization
- **Root user bypass** for superuser capabilities
- **Custom gate support** for application-specific permissions
- **Blade directive compatibility**

### 🧪 Comprehensive Testing
- **Feature tests** for all functionality
- **Unit tests** for services and repositories
- **Integration tests** for admin interface
- **Gate integration tests**
- **100% test coverage** for critical paths

### 📡 API Resources
- **RESTful API endpoints** for all operations
- **JSON API resources** with proper serialization
- **Pagination support**
- **Search and filtering**
- **Sanctum authentication** ready

## 🎯 Quick Start (Enhanced)

### 1. Install with Admin Interface
```bash
# Install with zero-configuration admin interface
composer require obrainwave/access-tree
php artisan accesstree:install --with-admin --with-gates
```

### 2. Access Admin Panel
Visit `/admin/accesstree` to access the complete admin interface.

### 3. Add Trait to User Model
```php
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
}
```

**That's it!** You now have a complete RBAC system with admin interface.

## 🎨 Admin Interface Features

### Dashboard
- **System statistics** (permissions, roles, users count)
- **Quick actions** for common tasks
- **System status** monitoring
- **Beautiful, responsive design**

### Permission Management
- **Create, edit, delete** permissions
- **Search and filter** functionality
- **Status management** (active/inactive)
- **Bulk operations** support
- **Real-time validation**

### Role Management
- **Role creation** with permission assignment
- **Permission management** per role
- **User assignment** tracking
- **Hierarchical role support**

### User Management
- **User role assignment**
- **Root user management**
- **Permission inheritance** display
- **Bulk role operations**

## 🔧 Advanced Configuration

### Service Architecture
```php
// Use the service directly
$accessTreeService = app(AccessTreeServiceInterface::class);

// Create permission
$response = $accessTreeService->createPermission([
    'name' => 'Manage Users',
    'status' => 1
]);

if ($response->isSuccess()) {
    // Permission created successfully
    $permission = $response->data;
}
```

### Repository Pattern
```php
// Use repositories for data access
$permissionRepo = app(PermissionRepository::class);
$permissions = $permissionRepo->search('user management');
```

### Caching System
```php
// Advanced caching with automatic invalidation
$cacheManager = app(CacheManager::class);
$userPermissions = $cacheManager->getUserPermissions($userId);
$cacheManager->clearUserCache($userId);
```

## 🚪 Laravel Gates Integration

### Automatic Gates
```php
// Gates are automatically registered
Gate::allows('manage-users');
Gate::allows('create-permissions');
Gate::allows('edit-roles');
```

### Custom Gates
```php
// Register custom gates
$gateService = app(GateService::class);
$gateService->registerCustomGate('publish-articles', 'publish_articles');
```

### Blade Directives
```php
@can('manage-users')
    <button>Manage Users</button>
@endcan

@can('create-permissions')
    <a href="/admin/accesstree/permissions/create">Create Permission</a>
@endcan
```

## 📡 API Usage

### RESTful Endpoints
```bash
# Get all permissions
GET /api/accesstree/permissions

# Create permission
POST /api/accesstree/permissions
{
    "name": "Manage Users",
    "status": 1
}

# Update permission
PUT /api/accesstree/permissions/1
{
    "name": "Manage All Users",
    "status": 1
}

# Delete permission
DELETE /api/accesstree/permissions/1
```

### API Resources
```php
// Automatic serialization with relationships
{
    "id": 1,
    "name": "Manage Users",
    "slug": "manage_users",
    "status": 1,
    "roles_count": 3,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --filter=AccessTreeServiceTest
php artisan test --filter=AdminInterfaceTest
php artisan test --filter=GateIntegrationTest
```

### Test Coverage
```bash
# Generate test coverage report
php artisan test --coverage
```

## 🎛️ Configuration Options

### Admin Interface
```php
// config/accesstree.php
'admin_interface' => [
    'enabled' => true,
    'route_prefix' => 'admin/accesstree',
    'middleware' => ['web', 'auth'],
    'layout' => 'accesstree::admin.layouts.app',
],
```

### Caching
```php
'cache_refresh_time' => 5, // minutes
'cache_driver' => 'redis', // or 'file', 'database'
```

### Gates
```php
'gates' => [
    'enabled' => true,
    'auto_register' => true,
],
```

## 🚀 Performance Features

### Advanced Caching
- **User-specific caching** for permissions and roles
- **Automatic cache invalidation** on changes
- **Configurable cache duration**
- **Multiple cache driver support**

### Database Optimization
- **Efficient queries** with proper joins
- **Pagination support** for large datasets
- **Index optimization** for fast lookups
- **Relationship eager loading**

### Memory Management
- **Lazy loading** for relationships
- **Memory-efficient collections**
- **Garbage collection** optimization

## 🔒 Security Features

### Input Validation
- **Comprehensive validation** for all inputs
- **SQL injection protection**
- **XSS prevention**
- **CSRF protection**

### Permission Security
- **Root user bypass** for superuser access
- **Permission inheritance** validation
- **Role hierarchy** enforcement
- **Audit logging** capabilities

## 📚 Documentation

### API Documentation
- **OpenAPI/Swagger** compatible
- **Request/response examples**
- **Error code documentation**
- **Authentication requirements**

### Developer Guides
- **Installation guide**
- **Configuration reference**
- **Customization examples**
- **Troubleshooting guide**

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for:
- **Coding standards**
- **Testing requirements**
- **Pull request process**
- **Issue reporting**

## 📈 Roadmap

### Upcoming Features
- **Multi-tenancy support** for SaaS applications
- **Permission groups** for better organization
- **Audit logging** for compliance
- **Real-time updates** with WebSockets
- **Mobile app support** with API
- **Advanced reporting** and analytics

### Integration Examples
- **Filament integration** examples
- **Livewire components** for dynamic UI
- **Inertia.js** integration
- **Vue.js** admin components

## 🆘 Support

- **GitHub Issues** for bug reports
- **Discord Community** for discussions
- **Email Support** for enterprise users
- **Documentation** for self-help

## 📄 License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.

---

**AccessTree Enhanced Edition** - The most powerful, flexible, and user-friendly RBAC system for Laravel applications. Built with modern Laravel patterns, featuring zero-configuration admin interface, and enterprise-grade performance.
