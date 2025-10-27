<?php

namespace Obrainwave\AccessTree\Console\Commands;

use Illuminate\Console\Command;
use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
// User model will be resolved dynamically

class TestAccessTreeCommand extends Command
{
    protected $signature = 'accesstree:test';
    protected $description = 'Test AccessTree functionality';

    public function handle()
    {
        $this->info('Testing AccessTree functionality...');

        try {
            // Test service registration
            $service = app(AccessTreeServiceInterface::class);
            $this->info('✓ Service registered successfully');

            // Test permission creation
            $permissionName = 'Test Permission ' . time(); // Make it unique
            $response = $service->createPermission([
                'name' => $permissionName,
                'status' => 1
            ]);

            if ($response->isSuccess()) {
                $this->info('✓ Permission creation works');
            } else {
                $this->error('✗ Permission creation failed: ' . $response->message);
                return 1;
            }

            // Test role creation
            $roleName = 'Test Role ' . time(); // Make it unique
            $permission = Permission::where('name', $permissionName)->first();
            $response = $service->createRole([
                'name' => $roleName,
                'status' => 1
            ], [$permission->id]);

            if ($response->isSuccess()) {
                $this->info('✓ Role creation works');
            } else {
                $this->error('✗ Role creation failed: ' . $response->message);
                return 1;
            }

            // Test user role assignment
            $userModel = getUserModelClass();
            $user = $userModel::first();
            if ($user) {
                $role = Role::where('name', $roleName)->first();
                $response = $service->assignRoleToUser($user->id, $role->id);

                if ($response->isSuccess()) {
                    $this->info('✓ User role assignment works');
                } else {
                    $this->error('✗ User role assignment failed: ' . $response->message);
                    return 1;
                }

                // Test permission checking
                $permissionSlug = \Illuminate\Support\Str::slug($permissionName, '_');
                if ($service->checkPermission($permissionSlug, $user->id)) {
                    $this->info('✓ Permission checking works');
                } else {
                    $this->error('✗ Permission checking failed');
                    return 1;
                }
            }

            // Cleanup test data
            $this->info('🧹 Cleaning up test data...');
            try {
                Permission::where('name', $permissionName)->delete();
                Role::where('name', $roleName)->delete();
                $this->info('✓ Test data cleaned up');
            } catch (\Exception $e) {
                $this->warn('⚠️ Could not clean up test data: ' . $e->getMessage());
            }

            $this->info('All tests passed! AccessTree is working correctly.');
            return 0;

        } catch (\Exception $e) {
            $this->error('Test failed with exception: ' . $e->getMessage());
            return 1;
        }
    }
}
