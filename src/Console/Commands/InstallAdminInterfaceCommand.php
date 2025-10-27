<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;

class InstallAdminInterfaceCommand extends Command
{
    protected $signature = 'accesstree:install-admin {--force : Overwrite existing files}';
    protected $description = 'Install AccessTree Admin Interface with zero-configuration setup';

    public function handle()
    {
        $this->info('Installing AccessTree Admin Interface...');

        // Publish views
        $this->call('vendor:publish', [
            '--tag' => 'accesstree-admin-views',
            '--force' => $this->option('force')
        ]);

        // Publish routes
        $this->call('vendor:publish', [
            '--tag' => 'accesstree-admin-routes',
            '--force' => $this->option('force')
        ]);

        // Create default permissions for admin interface
        $this->createAdminPermissions();

        $this->info('Admin interface installed successfully!');
        $this->info('Visit /admin/accesstree to access the admin panel');
        $this->info('Default admin permissions have been created.');
    }

    protected function createAdminPermissions()
    {
        $this->info('Creating admin interface permissions...');

        $permissions = [
            'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions',
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'manage_permissions', 'manage_roles', 'manage_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => ucwords(str_replace('_', ' ', $permission)),
                'slug' => $permission,
                'status' => 1
            ]);
        }

        // Create admin role with all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin Interface',
            'slug' => 'admin_interface',
            'status' => 1
        ]);

        $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id')->toArray();
        $adminRole->permissions()->sync($permissionIds);

        $this->info('Admin interface permissions created successfully!');
    }
}
