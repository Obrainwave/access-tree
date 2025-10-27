<?php

namespace Obrainwave\AccessTree\Repositories;

use App\Models\User;
use Obrainwave\AccessTree\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function find(int $id)
    {
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        
        // Try to get the user model from config
        if (!class_exists($userModel)) {
            $alternatives = [
                'App\\User',
                'App\\Models\\User',
                'Illuminate\\Foundation\\Auth\\User'
            ];
            
            foreach ($alternatives as $alternative) {
                if (class_exists($alternative)) {
                    $userModel = $alternative;
                    break;
                }
            }
        }
        
        if (!class_exists($userModel)) {
            throw new \Exception('User model not found. Please configure the user model in config/accesstree.php');
        }
        
        return $userModel::find($id);
    }

    public function assignRole(int $userId, $role): bool
    {
        $user = $this->find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Handle different role input types
        if (is_string($role)) {
            // Role slug
            $roleModel = \Obrainwave\AccessTree\Models\Role::where('slug', $role)->first();
        } elseif (is_int($role)) {
            // Role ID
            $roleModel = \Obrainwave\AccessTree\Models\Role::find($role);
        } elseif (is_object($role) && method_exists($role, 'id')) {
            // Role model
            $roleModel = $role;
        } else {
            throw new \Exception('Invalid role format');
        }

        if (!$roleModel) {
            throw new \Exception('Role not found');
        }

        $result = $user->roles()->syncWithoutDetaching([$roleModel->id]);
        // Return true if role was attached or if it was already attached (no changes needed)
        return !empty($result['attached']) || (empty($result['attached']) && empty($result['detached']));
    }

    public function removeRole(int $userId, $role): bool
    {
        $user = $this->find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Handle different role input types
        if (is_string($role)) {
            // Role slug
            $roleModel = \Obrainwave\AccessTree\Models\Role::where('slug', $role)->first();
        } elseif (is_int($role)) {
            // Role ID
            $roleModel = \Obrainwave\AccessTree\Models\Role::find($role);
        } elseif (is_object($role) && method_exists($role, 'id')) {
            // Role model
            $roleModel = $role;
        } else {
            throw new \Exception('Invalid role format');
        }

        if (!$roleModel) {
            throw new \Exception('Role not found');
        }

        $detachedCount = $user->roles()->detach([$roleModel->id]);
        return $detachedCount > 0;
    }

    public function syncRoles(int $userId, array $roles): bool
    {
        $user = $this->find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $roleIds = collect($roles)->map(function ($role) {
            if (is_numeric($role)) {
                return $role;
            }

            if ($role instanceof \Obrainwave\AccessTree\Models\Role) {
                return $role->id;
            }

            return \Obrainwave\AccessTree\Models\Role::where('slug', $role)->firstOrFail()->id;
        });

        $result = $user->roles()->sync($roleIds);
        return true; // sync always succeeds if no exceptions are thrown
    }

    public function getUserRoles(int $userId): Collection
    {
        $user = $this->find($userId);
        if (!$user) {
            return collect();
        }

        return $user->roles;
    }

    public function getUserPermissions(int $userId): Collection
    {
        $user = $this->find($userId);
        if (!$user) {
            return collect();
        }

        return $user->permissions();
    }

    public function isRootUser(int $userId): bool
    {
        $user = $this->find($userId);
        return $user ? $user->is_root_user : false;
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $userModel = config('accesstree.user_model', 'App\\Models\\User');
        $query = $userModel::query();

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with('roles')->paginate($perPage);
    }
}
