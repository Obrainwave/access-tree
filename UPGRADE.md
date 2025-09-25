# Upgrade Guide: AccessTree 1.x → 2.0.0

AccessTree 2.0.0 is a **major release** with breaking changes. This guide helps you migrate from 1.x to 2.0.0.

---

## Breaking Changes

1. **Seeder addes**
   - `php artisan accesstree:seed`.
   - `php artisan accesstree:seed --fresh` now **only clears AccessTree tables**.

2. **Helper & middleware updates**
   - `checkRoles()` added for role arrays.
   - Middleware can now handle `role:` prefix and multiple permissions more consistently.

3. **Configuration changes**
   - `cache_refresh_time`, `forbidden_redirect`, `assign_first_user_as_admin` defaults updated.
   - Assign first user as Admin is now **safe** — only affects AccessTree tables.

---

## Migration Steps

1. **Update composer**
```bash
composer require obrainwave/access-tree:^2.0
```

2. **Publish new config & seeder**
```bash
php artisan vendor:publish --tag=accesstree-config
php artisan vendor:publish --tag=accesstree-migrations
php artisan vendor:publish --tag=accesstree-seeders
```

3. **You may run seeder**
```bash
php artisan accesstree:seed
```

4. **Update User model**
```php
use Obrainwave\AccessTree\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
}
```

5. **Update your code**
* Remove deprecated helper calls in controller method.
* Use middleware access control on your routes by using `accesstree:role:admin` or multiple permissions `accesstree:view_user,add_user`.

## Notes
* Version 1.x is deprecated.
* All new features and bug fixes will only be released for 2.x.





