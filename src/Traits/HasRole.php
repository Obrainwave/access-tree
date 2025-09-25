<?php
namespace Obrainwave\AccessTree\Traits;

use Obrainwave\AccessTree\Models\Role;

trait HasRole
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        return $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function assignRoles($roles)
    {
        $role_ids = collect($roles)->map(function ($role) {
            if (is_numeric($role)) {
                return $role;
            }

            if ($role instanceof Role) {
                return $role->id;
            }

            return Role::where('slug', $role)->firstOrFail()->id;
        });

        $this->roles()->syncWithoutDetaching($role_ids);
    }

    public static function syncRoles($roles)
    {
        // sync will attach new ones and remove missing ones
        $this->roles()->sync($roles);

        return $this->roles;
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return $this->roles->contains('id', $role->id);
    }

    public function permissions()
    {
        return $this->roles->flatMap->permissions->unique('id');
    }

    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->contains('name', $permission);
        }

        return $this->permissions()->contains('id', $permission->id);
    }
}
