# Dynamic Table Management Configuration

## Overview

AccessTree now allows you to specify which tables should be managed dynamically in the admin interface. This gives you full control over which tables appear in the sidebar and can be managed through the universal admin interface.

## Configuration

Open your `config/accesstree.php` file and find the `managed_tables` configuration:

```php
'managed_tables' => [],
```

## Options

### Manage ALL Tables (Default)

Leave the array empty to manage all user tables in your database:

```php
'managed_tables' => [],
```

This is the default behavior and will show all tables except system tables like `migrations`, `permissions`, `roles`, etc.

### Manage Specific Tables

Specify an array of table names to manage only those tables:

```php
'managed_tables' => [
    'posts',
    'categories',
    'comments',
    'products',
    'orders',
],
```

Only the specified tables will appear in the sidebar and be accessible through the admin interface.

## How It Works

1. **Sidebar**: Only tables listed in `managed_tables` (or all tables if empty) will appear in the admin sidebar
2. **Access Control**: The `UniversalTableController` validates that a table is in the managed list before allowing access
3. **Error Handling**: If you try to access a table not in the list, you'll get a 403 error with a helpful message

## Examples

### E-commerce Application

```php
'managed_tables' => [
    'products',
    'product_categories',
    'product_images',
    'orders',
    'order_items',
    'customers',
    'coupons',
],
```

### Blog/CMS Application

```php
'managed_tables' => [
    'posts',
    'categories',
    'tags',
    'comments',
    'media',
    'pages',
],
```

### Learning Management System

```php
'managed_tables' => [
    'courses',
    'lessons',
    'enrollments',
    'assignments',
    'submissions',
    'grades',
    'students',
],
```

## Security Considerations

- Tables not in the `managed_tables` list are completely hidden from the admin interface
- Direct URL access to unmanaged tables will result in a 403 Forbidden error
- This prevents accidental exposure of sensitive tables
- Always review which tables you're making accessible through the admin interface

## Clearing Cache

After updating the configuration, clear the cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## Troubleshooting

### Table Not Showing in Sidebar

- Check that the table name is in the `managed_tables` array (if configured)
- Verify the table exists in your database
- Clear the cache: `php artisan config:clear`

### Getting 403 Error

- Make sure the table is listed in `managed_tables` configuration
- Check for typos in the table name
- Verify the table name matches exactly (case-sensitive in some databases)

### All Tables Not Showing

- Check your `managed_tables` configuration
- If it's empty `[]`, all tables should show
- If it has entries, only those tables will show

## Dashboard Table Cards

In addition to managing which tables are accessible, you can also control which tables appear as stat cards on the dashboard.

### Configuration

```php
'dashboard_table_cards' => [],
```

### Options

#### Show ALL Managed Tables on Dashboard (Default)

Leave the array empty to show all managed tables:

```php
'dashboard_table_cards' => [],
```

#### Show Specific Tables Only

Specify which tables should appear as dashboard cards:

```php
'dashboard_table_cards' => [
    'posts',
    'comments',
],
```

### How It Works

1. **`managed_tables`**: Controls which tables can be accessed/edited through the admin interface
2. **`dashboard_table_cards`**: Controls which of those managed tables appear as cards on the dashboard

### Example Scenarios

#### Scenario 1: Manage Many Tables, Show Only Key Ones

```php
// Manage all these tables through the admin interface
'managed_tables' => [
    'posts',
    'categories',
    'tags',
    'comments',
    'reviews',
    'settings',
    'logs',
],

// But only show the main ones on the dashboard
'dashboard_table_cards' => [
    'posts',
    'comments',
    'reviews',
],
```

#### Scenario 2: Show All Managed Tables

```php
// Manage specific tables
'managed_tables' => [
    'posts',
    'categories',
    'comments',
],

// Show all of them on dashboard
'dashboard_table_cards' => [],
```

#### Scenario 3: Different Views

```php
// Manage everything
'managed_tables' => [],

// But only show the most important ones on dashboard
'dashboard_table_cards' => [
    'orders',
    'products',
    'customers',
],
```

## Best Practices

1. **Be Selective**: Only add tables that need to be managed through the admin interface
2. **Name Matching**: Ensure table names match exactly between your database and configuration
3. **Regular Review**: Periodically review which tables are exposed
4. **Documentation**: Document why each table is included in your configuration
5. **Testing**: Test that tables are accessible after configuration changes
6. **Dashboard Focus**: Use `dashboard_table_cards` to keep the dashboard focused on the most important tables
7. **Separation of Concerns**: Remember that `managed_tables` controls access, while `dashboard_table_cards` controls visibility on the dashboard
