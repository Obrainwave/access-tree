<?php
namespace Obrainwave\AccessTree\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\RoleHasPermission;

trait AccessOperations
{
    /**
     * Create a permission
     */
    private function createPermission(array $data): string
    {
        if (Permission::where('name', $data['name'])->first()) {
            return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
        }

        $permission = Permission::create([
            'name'   => $data['name'],
            'slug'   => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 0,
        ]);

        return json_encode(['status' => 200, 'message' => 'Permission created successfully']);
    }

    /**
     * Update a permission
     */
    private function updatePermission(array $data): string
    {
        // Check if another permission already has this name
        if (Permission::where('name', $data['name'])->where('id', '!=', $data['data_id'])->first()) {
            return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
        }

        $permission = Permission::find($data['data_id']);
        if (! $permission) {
            return json_encode(['status' => 404, 'message' => 'Permission not found.']);
        }

        $permission->update([
            'name'   => $data['name'],
            'slug'   => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 0,
        ]);

        return json_encode(['status' => 200, 'message' => 'Permission updated successfully']);
    }

    /**
     * Create a role with permissions
     */
    private function createRole(array $data, array $permission_ids): string
    {
        if (empty($permission_ids)) {
            return json_encode(['status' => 422, 'message' => 'Sorry! permission_ids cannot be empty.']);
        }

        if (Role::where('name', $data['name'])->first()) {
            return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
        }

        $role = Role::create([
            'name'   => $data['name'],
            'slug'   => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 0,
        ]);

        $this->syncRolePermissions($role->id, $permission_ids);

        return json_encode(['status' => 200, 'message' => 'Role created successfully']);
    }

    /**
     * Update a role with permissions
     */
    private function updateRole(array $data, array $permission_ids): string
    {
        if (empty($permission_ids)) {
            return json_encode(['status' => 422, 'message' => 'Sorry! permission ids cannot be empty.']);
        }

        // Check if another role already has this name
        if (Role::where('name', $data['name'])->where('id', '!=', $data['data_id'])->first()) {
            return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
        }

        $role = Role::find($data['data_id']);
        if (! $role) {
            return json_encode(['status' => 404, 'message' => 'Role not found.']);
        }

        $role->update([
            'name'   => $data['name'],
            'slug'   => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 0,
        ]);

        $this->syncRolePermissions($role->id, $permission_ids);

        return json_encode(['status' => 200, 'message' => 'Role updated successfully']);
    }

    /**
     * Sync role permissions (helper method)
     */
    private function syncRolePermissions(int $roleId, array $permissionIds): void
    {
        // Delete existing permissions
        RoleHasPermission::where('role_id', $roleId)->delete();

        // Add new permissions
        foreach ($permissionIds as $id) {
            if (! Permission::find($id)) {
                continue;
            }
            RoleHasPermission::create([
                'role_id'       => $roleId,
                'permission_id' => $id,
            ]);
        }
    }

    /**
     * Check permission
     */
    public function checkPermission(string $permission): bool
    {
        return checkPermission($permission);
    }

    /**
     * Check permissions
     */
    public function checkPermissions(array $permissions): bool
    {
        return checkPermissions($permissions);
    }

    /**
     * Fetch permissions
     */
    public function fetchPermissions(array $options = [], int $status = 0): object
    {
        return fetchPermissions($options, $status);
    }

    /**
     * Fetch active permissions
     */
    public function fetchActivePermissions(): object
    {
        return fetchActivePermissions();
    }
    /**
     * Fetch Single permission
     */
    public function fetchPermission(int $permission_id): object | null
    {
        return fetchPermission($permission_id);
    }

    /**
     * Fetch roles
     */
    public function fetchRoles(array $options = [], int $status = 0): object
    {
        return fetchRoles($options, $status);
    }

    /**
     * Fetch Single role
     */
    public function fetchRole(int $role_id): object | null
    {
        return fetchRole($role_id);
    }

    /**
     * Fetch active roles
     */
    public function fetchActiveRoles(): object
    {
        return fetchActiveRoles();
    }

    /**
     * Fetch User roles
     */
    public function fetchUserRoles(int $user_id, bool $with_relation = true): object
    {
        return fetchUserRoles($user_id, $with_relation);
    }

    /**
     * Convenience methods for cleaner syntax
     */
    public function createPermissionSimple(string $name, int $status = 1): string
    {
        return $this->createAccess(['name' => $name, 'status' => $status], 'Permission');
    }

    public function updatePermissionSimple(int $id, string $name, int $status = 0): string
    {
        return $this->updateAccess(['data_id' => $id, 'name' => $name, 'status' => $status], 'Permission');
    }

    public function createRoleSimple(string $name, array $permission_ids, int $status = 1): string
    {
        return $this->createAccess(['name' => $name, 'status' => $status], 'Role', $permission_ids);
    }

    public function updateRoleSimple(int $id, string $name, array $permission_ids, int $status = 0): string
    {
        return $this->updateAccess(['data_id' => $id, 'name' => $name, 'status' => $status], 'Role', $permission_ids);
    }

    /**
     * Delete a permission or role
     */
    private function deletePermission(int $data_id): string
    {
        $permission = Permission::find($data_id);
        if (! $permission) {
            return json_encode(['status' => 404, 'message' => 'Permission not found.']);
        }

        // Check if permission is being used by any roles
        $roleCount = RoleHasPermission::where('permission_id', $data_id)->count();
        if ($roleCount > 0) {
            return json_encode(['status' => 422, 'message' => 'Cannot delete permission. It is assigned to ' . $roleCount . ' role(s).']);
        }

        $permission->delete();
        return json_encode(['status' => 200, 'message' => 'Permission deleted successfully']);
    }

    /**
     * Delete a role and its permissions
     */
    private function deleteRole(int $data_id): string
    {
        $role = Role::find($data_id);
        if (! $role) {
            return json_encode(['status' => 404, 'message' => 'Role not found.']);
        }

        // Check if role is assigned to any users
        $userCount = \Obrainwave\AccessTree\Models\UserRole::where('role_id', $data_id)->count();
        if ($userCount > 0) {
            return json_encode(['status' => 422, 'message' => 'Cannot delete role. It is assigned to ' . $userCount . ' user(s).']);
        }

        // Delete role permissions first
        RoleHasPermission::where('role_id', $data_id)->delete();

        // Then delete the role
        $role->delete();

        return json_encode(['status' => 200, 'message' => 'Role deleted successfully']);
    }

    /**
     * Convenience method for deleting permissions
     */
    public function deletePermissionSimple(int $id): string
    {
        return $this->deleteAccess($id, 'Permission');
    }

    /**
     * Convenience method for deleting roles
     */
    public function deleteRoleSimple(int $id): string
    {
        return $this->deleteAccess($id, 'Role');
    }

    public static function assignRoles(int $user_id, $roles)
    {
        $user = User::findOrFail($user_id);

        $role_ids = collect($roles)->map(function ($role) {
            if (is_numeric($role)) {
                return $role;
            }

            if ($role instanceof Role) {
                return $role->id;
            }

            return Role::where('slug', $role)->firstOrFail()->id;
        });

        $user->roles()->syncWithoutDetaching($role_ids);
    }

    public static function syncUserRoles(int $user_id, array $roles)
    {
        $user = User::findOrFail($user_id);

        // sync will attach new ones and remove missing ones
        $user->roles()->sync($roles);

        return $user->roles;
    }
}
