<?php

// User model will be resolved dynamically
use Illuminate\Support\Facades\Cache;

/**
 * Get the User model class dynamically
 */
function getUserModelClass()
{
    $userModel = config('accesstree.user_model', 'App\\Models\\User');
    
    if (!class_exists($userModel)) {
        $alternatives = [
            'App\\User',
            'App\\Models\\User',
            'Illuminate\\Foundation\\Auth\\User'
        ];
        
        foreach ($alternatives as $alternative) {
            if (class_exists($alternative)) {
                return $alternative;
            }
        }
        
        throw new \Exception('User model not found. Please configure the user model in config/accesstree.php');
    }
    
    return $userModel;
}
use Illuminate\Support\Facades\Schema;
use Obrainwave\AccessTree\Models\Permission;
use Obrainwave\AccessTree\Models\Role;
use Obrainwave\AccessTree\Models\RoleHasPermission;
use Obrainwave\AccessTree\Models\UserRole;
use Illuminate\Support\Str;

function createAccess(array $data, string $model, array $permission_ids = []): String
{
    $model = ucfirst($model);
    switch ($model) {
        case 'Permission':
            $permission = new Permission();
            if ($permission->where('name', $data['name'])->first()) {
                return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
            }
            $permission->name   = $data['name'];
            $permission->slug   = Str::slug($data['name'], '_');
            $permission->status = $data['status'] ?? 1;
            $permission->save();
            return json_encode(['status' => 200, 'message' => 'Permission created successfully']);
            break;

        case 'Role':
            if (empty($permission_ids)) {
                return json_encode(['status' => 422, 'message' => 'Sorry! permission_ids cannot be empty.']);
            }
            $role = new Role();
            if ($role->where('name', $data['name'])->first()) {
                return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
            }
            $role->name   = $data['name'];
            $role->slug   = Str::slug($data['name'], '_');
            $role->status = $data['status'] ?? 1;
            $role->save();

            foreach ($permission_ids as $id) {
                if (! Permission::find($id)) {
                    continue;
                }
                RoleHasPermission::create([
                    'role_id'       => $role->id,
                    'permission_id' => $id,
                ]);
            }
            return json_encode(['status' => 200, 'message' => 'Role created successfully']);
            break;

        default:
            return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
            break;
    }
}

function updateAccess(array $data, string $model, array $permission_ids = []): String
{
    $model = ucfirst($model);
    switch ($model) {
        case 'Permission':
            $permission = new Permission();
            if ($permission->where('name', $data['name'])->where('id', '!=', $data['data_id'])->first()) {
                return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
            }
            $permission         = $permission->find($data['data_id']);
            $permission->name   = $data['name'];
            $permission->slug   = Str::slug($data['name'], '_');
            $permission->status = $data['status'] ?? 0;
            $permission->save();
            return json_encode(['status' => 200, 'message' => 'Permission updated successfully']);
            break;

        case 'Role':
            if (empty($permission_ids)) {
                return json_encode(['status' => 422, 'message' => 'Sorry! permission ids cannot be empty.']);
            }
            $check_role = new Role();
            if ($check_role->where('name', $data['name'])->where('id', '!=', $data['data_id'])->first()) {
                return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
            }

            $role         = $check_role->find($data['data_id']);
            $role->name   = $data['name'];
            $role->slug   = Str::slug($data['name'], '_');
            $role->status = $data['status'] ?? 0;
            $role->save();

            $role_permission = new RoleHasPermission();

            $role_permission->where('role_id', $role->id)->delete();

            foreach ($permission_ids as $id) {
                $permission = new Permission();
                if (! $permission::find($id)) {
                    continue;
                }
                $role_permission::create([
                    'role_id'       => $role->id,
                    'permission_id' => $id,
                ]);
            }
            return json_encode(['status' => 200, 'message' => 'Role updated successfully']);
            break;

        default:
            return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
            break;
    }
}

function fetchRolePermissions(int $role_id): Object
{
    $role_permissions = RoleHasPermission::where('role_id', $role_id)->with(['permission', 'role'])->get();

    return $role_permissions;
}

function createUserRole(array $roles, int $user_id): String
{
    $user = getUserModelClass()::findOrFail($user_id);

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

    return json_encode(['status' => 200, 'message' => 'User Role(s) created successfully']);
}

function updateUserRole(array $roles, int $user_id): String
{
    $user = getUserModelClass()::findOrFail($user_id);

    // sync will attach new ones and remove missing ones
    $user->roles()->sync($roles);

    return json_encode(['status' => 200, 'message' => 'User Role(s) updated successfully']);
}

function isRootUser(int $user_id = null): bool
{
    if ($user_id) {
        if (getUserModelClass()::where('id', $user_id)->where('is_root_user', true)->first()) {
            return true;
        }
    } else {
        if (getUserModelClass()::where('id', auth()->user()->id)->where('is_root_user', true)->first()) {
            return true;
        }
    }

    return false;
}

function checkPermission(string $permission): bool
{
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    if (isRootUser($user->id)) {
        return true;
    }

    // Check if the permission exists
    $perm = Permission::where('slug', $permission)->first();
    if (! $perm) {
        // If in dev, log the warning
        if (app()->environment('local')) {
            \Log::warning("Permission '{$permission}' does not exist.");
        }
        return false; // safe default for production
    }

    // Cache the user permissions for x minutes set in the config file
    $permissions = Cache::remember(
        "user_{$user->id}_permissions",
        now()->addMinutes(config('accesstree.cache_refresh_time')),
        function () use ($user) {
            return UserRole::where('user_id', $user->id)
                ->with('role.permissions:id,slug')
                ->get()
                ->flatMap(fn($userRole) => $userRole->role->permissions->pluck('slug'))
                ->unique()
                ->toArray();
        }
    );

    return in_array($permission, $permissions);
}

function checkPermissions(array $permissions, bool $strict = false): bool
{
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    // Root user bypass
    if (isRootUser($user->id)) {
        return true;
    }

    // Warn in development if permission does not exist
    foreach ($permissions as $permSlug) {
        if (! Permission::where('slug', $permSlug)->exists() && app()->environment('local')) {
            \Log::warning("Permission '{$permSlug}' does not exist.");
        }
    }

    // Cache the user's permissions for efficiency
    $userPermissions = Cache::remember(
        "user_{$user->id}_permissions",
        now()->addMinutes(config('accesstree.cache_refresh_time')),
        function () use ($user) {
            return UserRole::where('user_id', $user->id)
                ->with('role.permissions:id,slug')
                ->get()
                ->flatMap(fn($userRole) => $userRole->role->permissions->pluck('slug'))
                ->unique()
                ->toArray();
        }
    );

    if ($strict) {
        // User must have ALL permissions
        return empty(array_diff($permissions, $userPermissions));
    } else {
        // User must have AT LEAST ONE permission
        return ! empty(array_intersect($permissions, $userPermissions));
    }
}

function checkRole(string $role): bool
{
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    // Root users bypass role checks
    if (isRootUser($user->id)) {
        return true;
    }

    // Check if the role exists
    $roleModel = Role::where('slug', $role)->first();
    if (! $roleModel) {
        // In dev log warning
        if (app()->environment('local')) {
            \Log::warning("Role '{$role}' does not exist.");
        }
        return false; // safe default
    }

    // Cache the user roles for x minutes (set in config)
    $roles = Cache::remember(
        "user_{$user->id}_roles",
        now()->addMinutes(config('accesstree.cache_refresh_time')),
        function () use ($user) {
            return $user->roles()
                ->pluck('slug')
                ->unique()
                ->toArray();
        }
    );

    return in_array($role, $roles);
}

function checkRoles(array $roles, bool $strict = false): bool
{
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    // Root user bypass
    if (isRootUser($user->id)) {
        return true;
    }

    // Warn in development if role does not exist
    foreach ($roles as $roleSlug) {
        if (! Role::where('slug', $roleSlug)->exists() && app()->environment('local')) {
            \Log::warning("Role '{$roleSlug}' does not exist.");
        }
    }

    // Cache the user's roles for efficiency
    $userRoles = Cache::remember(
        "user_{$user->id}_roles",
        now()->addMinutes(config('accesstree.cache_refresh_time')),
        function () use ($user) {
            return $user->roles()
                ->pluck('slug')
                ->unique()
                ->toArray();
        }
    );

    if ($strict) {
        // User must have ALL roles
        return empty(array_diff($roles, $userRoles));
    } else {
        // User must have AT LEAST ONE role
        return ! empty(array_intersect($roles, $userRoles));
    }
}

function fetchPermissions(array $options = [], int $status = 0): Object
{
    $data      = arrayToJson($options);
    $order     = isset($data->order) && in_array($data->order, ['asc', 'desc']) ? strtolower($data->order) : 'desc';
    $order_ref = isset($data->order_ref) && Schema::hasColumn('permissions', $data->order_ref) ? $data->order_ref : 'name';
    $paginate  = isset($data->paginate) ? $data->paginate : false;
    $per_page  = isset($data->per_page) ? $data->per_page : 10;

    if ($status === 1) {
        $permissions = Permission::where('status', $status)->orderBy($order_ref, $order);
    } else {
        $permissions = Permission::orderBy($order_ref, $order);
    }

    return $paginate
        ? $permissions->paginate($per_page)
        : $permissions->get();
}

function fetchActivePermissions(): Object
{
    $permissions = Permission::where('status', 1)->get();

    return $permissions;
}

function fetchPermission(int $permission_id): Object | null
{
    $permission = Permission::find($permission_id);

    return $permission ?? null;
}

function fetchRoles(array $options = [], int $status = 0): Object
{
    $data      = arrayToJson($options);
    $order     = isset($data->order) && in_array($data->order, ['asc', 'desc']) ? strtolower($data->order) : 'desc';
    $order_ref = isset($data->order_ref) && Schema::hasColumn('roles', $data->order_ref) ? $data->order_ref : 'name';
    $paginate  = isset($data->paginate) ? $data->paginate : false;
    $per_page  = isset($data->per_page) ? $data->per_page : 10;
    $relation  = isset($data->with_relation) ? $data->with_relation : false;

    $roles = Role::query();

    if ($status === 1) {
        $roles->where('status', $status);
    }

    if ($relation) {
        $roles->with('permissions');
    }

    $roles->orderBy($order_ref, $order);

    return $paginate
        ? $roles->paginate($per_page)
        : $roles->get();
}

function fetchActiveRoles(): Object
{
    $roles = Role::where('status', 1)->get();

    return $roles;
}

function fetchRole(int $role_id): Object | null
{
    $role = Role::where('id', $role_id)->with('rolePermissions.permission')->first();

    return $role ?? null;
}

function fetchUserRoles(int $user_id, bool $with_relation = true): Object
{
    if (! $with_relation) {
        $user = getUserModelClass()::where('id', $user_id)->with('roles')->first();
    } else {
        $user = getUserModelClass()::where('id', $user_id)->with('roles.permissions')->first();
    }

    return $user->roles;
}

function arrayToJson(array $data): object
{
    $data = json_decode(json_encode($data), false);

    return $data;
}
