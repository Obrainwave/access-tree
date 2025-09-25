<?php
namespace Obrainwave\AccessTree\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // post
            'view post', 'create post', 'edit post', 'delete post',
            // user
            'view user', 'create user', 'edit user', 'delete user',
            // role
            'view role', 'create role', 'edit role', 'delete role', 'assign role', 'revoke role',
            // Permissions
            'view permission', 'add permission', 'edit permission', 'delete permission', 'assign permission', 'revoke permission',
            // Settings & Admin
            'access dashboard data', 'manage settings', 'manage system',
        ];

        $permissionModels = [];
        $roleModels       = [];

        // === Seed Permissions ===
        if (config('accesstree.seed_permissions')) {
            foreach ($permissions as $name) {
                $permissionModels[$name] = Permission::firstOrCreate(['name' => ucwords($name), 'slug' => \Illuminate\Support\Str::slug($name, '_')]);
            }
        }

        // === Seed role ===
        if (config('accesstree.seed_roles')) {
            $roles = [
                'Super Admin' => $permissions,
                'Admin'       => [
                    'view post', 'create post', 'edit post', 'delete post',
                    'view user', 'create user', 'edit user', 'delete user',
                    'view role', 'create role', 'edit role', 'delete role', 'assign role', 'revoke role',
                    'access dashboard data', 'manage settings', 'manage system',
                ],
                'Editor'      => ['view post', 'create post', 'edit post', 'delete post', 'access dashboard'],
            ];

            foreach ($roles as $roleName => $perms) {
                $roleModel = Role::firstOrCreate([
                    'name' => ucwords($roleName),
                    'slug' => \Illuminate\Support\Str::slug($roleName, '_'),
                ]);

                if (config('accesstree.seed_permissions')) {
                    $roleModel->permissions()->sync(
                        collect($perms)
                            ->map(fn($p) => $permissionModels[$p]->id ?? null)
                            ->filter()
                            ->toArray()
                    );
                }

                $roleModels[$roleName] = $roleModel;
            }
        }

        // === Assign First User As Admin ===
        if (config('accesstree.assign_first_user_as_admin') && isset($roleModels['Admin'])) {
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->roles()->syncWithoutDetaching([$roleModels['Admin']->id]);
            }
        }
    }
}
