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
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | Specify which cache driver to use for AccessTree caching.
    | For better performance with tagging support, use 'redis' or 'memcached'.
    | For basic setups, 'file' or 'database' will work but without tagging.
    |
    */
    'cache_driver' => config('cache.default'),

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

    /*
    |--------------------------------------------------------------------------
    | Admin Interface Configuration
    |--------------------------------------------------------------------------
    | Configuration for the zero-configuration admin interface.
    */
    'admin_interface' => [
        'enabled' => true,
        'route_prefix' => 'admin/accesstree',
        'middleware' => ['web', 'auth'],
        'layout' => 'accesstree::admin.layouts.app',
        
        // Auto-generated resources
        'resources' => [
            'permissions' => [
                'enabled' => true,
                'searchable_fields' => ['name', 'slug'],
                'table_columns' => [
                    'name' => 'Name',
                    'slug' => 'Slug', 
                    'status' => 'Status',
                    'created_at' => 'Created',
                    'actions' => 'Actions'
                ],
                'form_fields' => [
                    'name' => ['type' => 'text', 'required' => true],
                    'status' => ['type' => 'select', 'options' => [1 => 'Active', 0 => 'Inactive']]
                ]
            ],
            'roles' => [
                'enabled' => true,
                'searchable_fields' => ['name', 'slug'],
                'table_columns' => [
                    'name' => 'Name',
                    'slug' => 'Slug',
                    'permissions_count' => 'Permissions',
                    'users_count' => 'Users',
                    'status' => 'Status',
                    'actions' => 'Actions'
                ]
            ],
            'users' => [
                'enabled' => true,
                'searchable_fields' => ['name', 'email'],
                'table_columns' => [
                    'name' => 'Name',
                    'email' => 'Email',
                    'roles_count' => 'Roles',
                    'is_root_user' => 'Root User',
                    'created_at' => 'Created',
                    'actions' => 'Actions'
                ]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Gates Integration
    |--------------------------------------------------------------------------
    | Enable Laravel Gates integration for seamless authorization.
    */
    'gates' => [
        'enabled' => true,
        'auto_register' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model Configuration
    |--------------------------------------------------------------------------
    |
    | Specify the User model class to use for user management.
    | The package will automatically detect common User model locations.
    */
    'user_model' => env('ACCESSTREE_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Admin Interface Styling
    |--------------------------------------------------------------------------
    |
    | Configure the CSS framework and styling for the admin interface.
    | This section allows you to customize the appearance and behavior of
    | the admin panel to match your application's design requirements.
    |
    | Supported frameworks: 'bootstrap', 'tailwind', 'custom'
    | Available themes: 'modern', 'classic', 'minimal'
    |
    */
    'styling' => [
        /*
        |--------------------------------------------------------------------------
        | CSS Framework
        |--------------------------------------------------------------------------
        |
        | Choose the CSS framework for the admin interface:
        | - 'bootstrap': Uses Bootstrap 5 for styling (default)
        | - 'tailwind': Uses Tailwind CSS for styling
        | - 'custom': Disables framework-specific styles, use your own CSS
        |
        */
        'framework' => env('ACCESSTREE_CSS_FRAMEWORK', 'bootstrap'),

        /*
        |--------------------------------------------------------------------------
        | Theme Selection
        |--------------------------------------------------------------------------
        |
        | Choose the visual theme for the admin interface:
        | - 'modern': Contemporary design with gradients and animations (default)
        | - 'classic': Traditional admin panel look with clean lines
        | - 'minimal': Simplified design with minimal visual elements
        |
        */
        'theme' => env('ACCESSTREE_THEME', 'modern'),

        /*
        |--------------------------------------------------------------------------
        | Custom CSS
        |--------------------------------------------------------------------------
        |
        | Add your own CSS styles to override or extend the default admin styling.
        | This is useful for:
        | - Branding: Add your company colors, logos, fonts
        | - Custom layouts: Modify spacing, sizing, positioning
        | - Theme overrides: Change specific elements without editing core files
        | - Integration: Match admin panel with your main application design
        |
        | Examples:
        | - Change primary color: '.btn-primary { background-color: #your-color !important; }'
        | - Custom fonts: '@import url("https://fonts.googleapis.com/css2?family=YourFont");'
        | - Brand logo: '.admin-logo { content: url("/path/to/your/logo.png"); }'
        | - Custom sidebar: '.sidebar { background: linear-gradient(45deg, #color1, #color2); }'
        |
        | Note: Use !important sparingly and test thoroughly across all pages.
        |
        */
        'custom_css' => env('ACCESSTREE_CUSTOM_CSS', ''),

        /*
        |--------------------------------------------------------------------------
        | Custom JavaScript
        |--------------------------------------------------------------------------
        |
        | Add your own JavaScript code to enhance the admin interface functionality.
        | This is useful for:
        | - Custom interactions: Add click handlers, form validations
        | - Third-party integrations: Analytics, chat widgets, etc.
        | - Enhanced UX: Custom animations, tooltips, notifications
        | - API calls: Custom AJAX requests, real-time updates
        |
        | Examples:
        | - Analytics tracking: 'gtag("config", "GA_MEASUREMENT_ID");'
        | - Custom notifications: 'toastr.success("Welcome to admin panel!");'
        | - Form enhancements: '$(".form-control").on("focus", function() { ... });'
        | - Real-time updates: 'setInterval(() => { fetchUpdates(); }, 30000);'
        |
        | Note: Ensure your JavaScript is compatible with the admin interface
        | and doesn't conflict with existing functionality.
        |
        */
        'custom_js' => env('ACCESSTREE_CUSTOM_JS', ''),

        /*
        |--------------------------------------------------------------------------
        | Dark Mode
        |--------------------------------------------------------------------------
        |
        | Enable dark mode by default for the admin interface.
        | Users can still toggle between light and dark modes using the UI switch.
        | - true: Dark mode enabled by default
        | - false: Light mode enabled by default (default)
        |
        */
        'dark_mode' => env('ACCESSTREE_DARK_MODE', false),

        /*
        |--------------------------------------------------------------------------
        | Animations
        |--------------------------------------------------------------------------
        |
        | Enable/disable animations and transitions in the admin interface.
        | - true: Smooth animations and transitions enabled (default)
        | - false: Disable animations for better performance or accessibility
        |
        | Note: Disabling animations may improve performance on slower devices
        | but reduces the visual appeal of the interface.
        |
        */
        'animations' => env('ACCESSTREE_ANIMATIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Table Management
    |--------------------------------------------------------------------------
    |
    | Configure which tables should be managed dynamically in the admin interface.
    | Leave empty array [] to manage ALL tables.
    | Or specify an array of table names to manage only those specific tables.
    |
    | Example: ['posts', 'categories', 'comments']
    | Example: [] (manage all tables)
    |
    */
    'managed_tables' => [],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Table Cards
    |--------------------------------------------------------------------------
    |
    | Configure which tables should be displayed as cards on the dashboard.
    | Leave empty array [] to show ALL managed tables.
    | Or specify an array of table names to show only those specific tables.
    |
    | Note: Only tables in 'managed_tables' will be shown on the dashboard.
    |       This allows you to have more managed tables but only display specific ones.
    |
    | Example: ['posts', 'comments'] (show only posts and comments cards)
    | Example: [] (show all managed tables as cards)
    |
    */
    'dashboard_table_cards' => [],

    /*
    |--------------------------------------------------------------------------
    | Admin Interface Favicon
    |--------------------------------------------------------------------------
    |
    | Configure the favicon for the admin interface.
    | You can provide a path to an image file (relative to public folder),
    | or leave null to use the default.
    |
    */
    'admin_favicon' => env('ACCESSTREE_ADMIN_FAVICON', null),
];
