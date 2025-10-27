<?php

namespace Obrainwave\AccessTree\Repositories;

use Illuminate\Support\Str;
use Obrainwave\AccessTree\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    public function create(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? 1,
        ]);
    }

    public function update(int $id, array $data): Permission
    {
        $permission = $this->find($id);
        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        $permission->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '_'),
            'status' => $data['status'] ?? $permission->status,
        ]);

        return $permission->fresh();
    }

    public function delete(int $id): bool
    {
        $permission = $this->find($id);
        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        return $permission->delete();
    }

    public function find(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    public function getActivePermissions(): Collection
    {
        return Permission::where('status', 1)->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function search(string $query): Collection
    {
        return Permission::where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = Permission::query();

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('slug', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
}
