<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AccessTree Seeder Options
    |--------------------------------------------------------------------------
    | You can toggle default package features here.
    |
    | - seed_permissions: Create default permissions.
    | - seed_roles: Create default roles and assign permissions.
    | - assign_first_user_as_admin: Attach the Admin role to the first user.
    |
    */

    'seed_permissions'            => true,

    'seed_roles'                  => true,

    'assign_first_user_as_admin'  => true,

    /*
    |--------------------------------------------------------------------------
    | User Permissions Cache Refresh Time
    |--------------------------------------------------------------------------
    |
    | Defines the number of minutes to cache a user's permissions. Adjust this
    | value to control how often the cache is refreshed. Lower values mean
    | changes to roles/permissions take effect faster, higher values improve
    | performance by reducing database queries.
    |
    */
    'cache_refresh_time' => 5,

    /*
    |--------------------------------------------------------------------------
    | Default redirect route when access is forbidden
    |--------------------------------------------------------------------------
    | This is used by the CheckAccessMiddleware to avoid redirect loops.
    | Set this to a safe route name in your app, like 'dashboard' or 'home'.
    */
    'forbidden_redirect' => 'home',

    /*
    |--------------------------------------------------------------------------
    | Fresh Seed Tables
    |--------------------------------------------------------------------------
    | Tables to be truncated when running "php artisan accesstree:seed --fresh".
    | Only these tables will be cleared instead of refreshing the entire DB.
    */
    'fresh_tables' => [
        'permissions',
        'roles',
        'role_has_permissions',
        'user_roles',
    ],
];
