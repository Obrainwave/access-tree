<?php

namespace Obrainwave\AccessTree\Repositories;

use Illuminate\Support\Str;
use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\RoleHasPermission;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    public function create(array $data, array $permissionIds = []): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 1,
        ]);

        if (!empty($permissionIds)) {
            $this->syncPermissions($role->id, $permissionIds);
        }

        return $role;
    }

    public function update(int $id, array $data, array $permissionIds = []): Role
    {
        $role = $this->find($id);
        if (!$role) {
            throw new \Exception('Role not found');
        }

        $role->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? $role->status,
        ]);

        if (!empty($permissionIds)) {
            $this->syncPermissions($role->id, $permissionIds);
        }

        return $role->fresh();
    }

    public function delete(int $id): bool
    {
        $role = $this->find($id);
        if (!$role) {
            throw new \Exception('Role not found');
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            throw new \Exception('Cannot delete role. It is assigned to users.');
        }

        // Delete role permissions first
        RoleHasPermission::where('role_id', $id)->delete();

        return $role->delete();
    }

    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    public function getActiveRoles(): Collection
    {
        return Role::where('status', 1)->get();
    }

    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    public function search(string $query): Collection
    {
        return Role::where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = Role::query();

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('slug', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('permissions')->paginate($perPage);
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        // Delete existing permissions
        RoleHasPermission::where('role_id', $roleId)->delete();

        // Add new permissions
        foreach ($permissionIds as $permissionId) {
            RoleHasPermission::create([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function getRolePermissions(int $roleId): Collection
    {
        return RoleHasPermission::where('role_id', $roleId)
            ->with('permission')
            ->get()
            ->pluck('permission');
    }
}
