<?php

namespace Obrainwave\AccessTree\Services;

use Obrainwave\AccessTree\Contracts\AccessTreeServiceInterface;
use Obrainwave\AccessTree\Responses\AccessTreeResponse;
use Obrainwave\AccessTree\Repositories\PermissionRepository;
use Obrainwave\AccessTree\Repositories\RoleRepository;
use Obrainwave\AccessTree\Repositories\UserRepository;
use Obrainwave\AccessTree\Services\CacheManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;

class AccessTreeService implements AccessTreeServiceInterface
{
    public function __construct(
        private PermissionRepository $permissionRepository,
        private RoleRepository $roleRepository,
        private UserRepository $userRepository,
        private CacheManager $cacheManager
    ) {}

    public function createPermission(array $data): AccessTreeResponse
    {
        try {
            $validator = $this->validatePermissionData($data);
            if ($validator->fails()) {
                return AccessTreeResponse::error($validator->errors()->first(), 422, $validator->errors()->toArray());
            }

            $permission = $this->permissionRepository->create($data);
            $this->cacheManager->clearPermissionCache();
            
            return AccessTreeResponse::success('Permission created successfully', $permission);
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to create permission: ' . $e->getMessage(), 500);
        }
    }

    public function updatePermission(int $id, array $data): AccessTreeResponse
    {
        try {
            $validator = $this->validatePermissionData($data, $id);
            if ($validator->fails()) {
                return AccessTreeResponse::error($validator->errors()->first(), 422, $validator->errors()->toArray());
            }

            $permission = $this->permissionRepository->update($id, $data);
            $this->cacheManager->clearPermissionCache();
            
            return AccessTreeResponse::success('Permission updated successfully', $permission);
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to update permission: ' . $e->getMessage(), 500);
        }
    }

    public function deletePermission(int $id): AccessTreeResponse
    {
        try {
            $this->permissionRepository->delete($id);
            $this->cacheManager->clearPermissionCache();
            
            return AccessTreeResponse::success('Permission deleted successfully');
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to delete permission: ' . $e->getMessage(), 500);
        }
    }

    public function createRole(array $data, array $permissionIds = []): AccessTreeResponse
    {
        try {
            $validator = $this->validateRoleData($data);
            if ($validator->fails()) {
                return AccessTreeResponse::error($validator->errors()->first(), 422, $validator->errors()->toArray());
            }

            $role = $this->roleRepository->create($data, $permissionIds);
            $this->cacheManager->clearRoleCache();
            
            return AccessTreeResponse::success('Role created successfully', $role);
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to create role: ' . $e->getMessage(), 500);
        }
    }

    public function updateRole(int $id, array $data, array $permissionIds = []): AccessTreeResponse
    {
        try {
            $validator = $this->validateRoleData($data);
            if ($validator->fails()) {
                return AccessTreeResponse::error($validator->errors()->first(), 422, $validator->errors()->toArray());
            }

            $role = $this->roleRepository->update($id, $data, $permissionIds);
            $this->cacheManager->clearRoleCache();
            
            return AccessTreeResponse::success('Role updated successfully', $role);
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to update role: ' . $e->getMessage(), 500);
        }
    }

    public function deleteRole(int $id): AccessTreeResponse
    {
        try {
            $this->roleRepository->delete($id);
            $this->cacheManager->clearRoleCache();
            
            return AccessTreeResponse::success('Role deleted successfully');
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to delete role: ' . $e->getMessage(), 500);
        }
    }

    public function assignRoleToUser(int $userId, $role): AccessTreeResponse
    {
        try {
            $this->userRepository->assignRole($userId, $role);
            $this->cacheManager->clearUserCache($userId);
            
            return AccessTreeResponse::success('Role assigned to user successfully');
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to assign role: ' . $e->getMessage(), 500);
        }
    }

    public function removeRoleFromUser(int $userId, $role): AccessTreeResponse
    {
        try {
            $this->userRepository->removeRole($userId, $role);
            $this->cacheManager->clearUserCache($userId);
            
            return AccessTreeResponse::success('Role removed from user successfully');
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to remove role: ' . $e->getMessage(), 500);
        }
    }

    public function syncUserRoles(int $userId, array $roles): AccessTreeResponse
    {
        try {
            $this->userRepository->syncRoles($userId, $roles);
            $this->cacheManager->clearUserCache($userId);
            
            return AccessTreeResponse::success('User roles synchronized successfully');
        } catch (\Exception $e) {
            return AccessTreeResponse::error('Failed to sync roles: ' . $e->getMessage(), 500);
        }
    }

    public function getUserPermissions(int $userId): array
    {
        return $this->cacheManager->getUserPermissions($userId);
    }

    public function getUserRoles(int $userId): array
    {
        return $this->cacheManager->getUserRoles($userId);
    }

    public function checkPermission(string $permission, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }

        if ($this->userRepository->isRootUser($userId)) {
            return true;
        }

        $userPermissions = $this->getUserPermissions($userId);
        return in_array($permission, $userPermissions);
    }

    public function checkPermissions(array $permissions, bool $strict = false, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }

        if ($this->userRepository->isRootUser($userId)) {
            return true;
        }

        $userPermissions = $this->getUserPermissions($userId);
        
        if ($strict) {
            return empty(array_diff($permissions, $userPermissions));
        }
        
        return !empty(array_intersect($permissions, $userPermissions));
    }

    public function checkRole(string $role, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }

        if ($this->userRepository->isRootUser($userId)) {
            return true;
        }

        $userRoles = $this->getUserRoles($userId);
        return in_array($role, $userRoles);
    }

    public function checkRoles(array $roles, bool $strict = false, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) {
            return false;
        }

        if ($this->userRepository->isRootUser($userId)) {
            return true;
        }

        $userRoles = $this->getUserRoles($userId);
        
        if ($strict) {
            return empty(array_diff($roles, $userRoles));
        }
        
        return !empty(array_intersect($roles, $userRoles));
    }

    private function validatePermissionData(array $data, ?int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'boolean'
        ];

        if ($id) {
            $rules['name'] .= '|unique:permissions,name,' . $id;
        } else {
            $rules['name'] .= '|unique:permissions,name';
        }

        return Validator::make($data, $rules);
    }

    private function validateRoleData(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'status' => 'boolean'
        ]);
    }

    /**
     * Backward compatibility methods
     * These methods support the old API that users may still be using
     */

    /**
     * Fetch permissions with optional filtering and pagination
     * 
     * @param array $options ['order' => 'asc|desc', 'order_ref' => 'column_name', 'paginate' => bool, 'per_page' => int]
     * @param int $status 0 for all, 1 for active only
     * @return object
     */
    public function fetchPermissions(array $options = [], int $status = 0): object
    {
        $data = json_decode(json_encode($options), false);
        $order = isset($data->order) && in_array($data->order, ['asc', 'desc']) ? strtolower($data->order) : 'desc';
        $orderRef = isset($data->order_ref) && \Schema::hasColumn('permissions', $data->order_ref) ? $data->order_ref : 'name';
        $paginate = isset($data->paginate) ? $data->paginate : false;
        $perPage = isset($data->per_page) ? $data->per_page : 10;

        $query = \Obrainwave\AccessTree\Models\Permission::orderBy($orderRef, $order);

        if ($status === 1) {
            $query->where('status', $status);
        }

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Fetch active permissions only
     * 
     * @return object
     */
    public function fetchActivePermissions(): object
    {
        return \Obrainwave\AccessTree\Models\Permission::where('status', 1)->get();
    }

    /**
     * Fetch a single permission by ID
     * 
     * @param int $permissionId
     * @return object|null
     */
    public function fetchPermission(int $permissionId): ?object
    {
        return \Obrainwave\AccessTree\Models\Permission::find($permissionId);
    }

    /**
     * Fetch roles with optional filtering and pagination
     * 
     * @param array $options ['order' => 'asc|desc', 'order_ref' => 'column_name', 'paginate' => bool, 'per_page' => int, 'with_relation' => bool]
     * @param int $status 0 for all, 1 for active only
     * @return object
     */
    public function fetchRoles(array $options = [], int $status = 0): object
    {
        $data = json_decode(json_encode($options), false);
        $order = isset($data->order) && in_array($data->order, ['asc', 'desc']) ? strtolower($data->order) : 'desc';
        $orderRef = isset($data->order_ref) && \Schema::hasColumn('roles', $data->order_ref) ? $data->order_ref : 'name';
        $paginate = isset($data->paginate) ? $data->paginate : false;
        $perPage = isset($data->per_page) ? $data->per_page : 10;
        $withRelation = isset($data->with_relation) ? $data->with_relation : false;

        $query = \Obrainwave\AccessTree\Models\Role::query();

        if ($status === 1) {
            $query->where('status', $status);
        }

        if ($withRelation) {
            $query->with('permissions');
        }

        $query->orderBy($orderRef, $order);

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Fetch active roles only
     * 
     * @return object
     */
    public function fetchActiveRoles(): object
    {
        return \Obrainwave\AccessTree\Models\Role::where('status', 1)->get();
    }

    /**
     * Fetch a single role by ID
     * 
     * @param int $roleId
     * @return object|null
     */
    public function fetchRole(int $roleId): ?object
    {
        return \Obrainwave\AccessTree\Models\Role::where('id', $roleId)->with('permissions')->first();
    }

    /**
     * Fetch user roles
     * 
     * @param int $userId
     * @param bool $withRelation Include role permissions
     * @return object
     */
    public function fetchUserRoles(int $userId, bool $withRelation = true): object
    {
        $userModelClass = $this->getUserModelClass();
        $user = $userModelClass::where('id', $userId)->with($withRelation ? 'roles.permissions' : 'roles')->first();
        
        return $user ? $user->roles : collect();
    }

    /**
     * Create access (Permission or Role) - backward compatibility method
     * 
     * @param array $data
     * @param string $model 'Permission' or 'Role'
     * @param array $permission_ids Only used when creating a Role
     * @return string JSON encoded response
     */
    public function createAccess(array $data, string $model, array $permission_ids = []): string
    {
        $model = ucfirst($model);
        
        try {
            switch ($model) {
                case 'Permission':
                    $permission = \Obrainwave\AccessTree\Models\Permission::where('name', $data['name'])->first();
                    if ($permission) {
                        return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
                    }
                    
                    $permission = \Obrainwave\AccessTree\Models\Permission::create([
                        'name' => $data['name'],
                        'slug' => \Illuminate\Support\Str::slug($data['name'], '_'),
                        'status' => $data['status'] ?? 1,
                    ]);
                    
                    $this->cacheManager->clearPermissionCache();
                    return json_encode(['status' => 200, 'message' => 'Permission created successfully']);
                    
                case 'Role':
                    if (empty($permission_ids)) {
                        return json_encode(['status' => 422, 'message' => 'Sorry! permission_ids cannot be empty.']);
                    }
                    
                    $role = \Obrainwave\AccessTree\Models\Role::where('name', $data['name'])->first();
                    if ($role) {
                        return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
                    }
                    
                    $role = \Obrainwave\AccessTree\Models\Role::create([
                        'name' => $data['name'],
                        'slug' => \Illuminate\Support\Str::slug($data['name'], '_'),
                        'status' => $data['status'] ?? 1,
                    ]);
                    
                    foreach ($permission_ids as $id) {
                        if (!\Obrainwave\AccessTree\Models\Permission::find($id)) {
                            continue;
                        }
                        \Obrainwave\AccessTree\Models\RoleHasPermission::create([
                            'role_id' => $role->id,
                            'permission_id' => $id,
                        ]);
                    }
                    
                    $this->cacheManager->clearRoleCache();
                    return json_encode(['status' => 200, 'message' => 'Role created successfully']);
                    
                default:
                    return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'message' => 'Failed to create ' . strtolower($model) . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Update access (Permission or Role) - backward compatibility method
     * 
     * @param array $data Must include 'data_id' key
     * @param string $model 'Permission' or 'Role'
     * @param array $permission_ids Only used when updating a Role
     * @return string JSON encoded response
     */
    public function updateAccess(array $data, string $model, array $permission_ids = []): string
    {
        $model = ucfirst($model);
        
        try {
            switch ($model) {
                case 'Permission':
                    $permission = \Obrainwave\AccessTree\Models\Permission::find($data['data_id']);
                    if (!$permission) {
                        return json_encode(['status' => 404, 'message' => 'Permission not found.']);
                    }
                    
                    $validator = $this->validatePermissionData($data, $permission->id);
                    if ($validator->fails()) {
                        return json_encode(['status' => 422, 'message' => $validator->errors()->first()]);
                    }
                    
                    $permission = $this->permissionRepository->update($permission->id, $data);
                    $this->cacheManager->clearPermissionCache();
                    return json_encode(['status' => 200, 'message' => 'Permission updated successfully']);
                    
                case 'Role':
                    $role = \Obrainwave\AccessTree\Models\Role::find($data['data_id']);
                    if (!$role) {
                        return json_encode(['status' => 404, 'message' => 'Role not found.']);
                    }
                    
                    $validator = $this->validateRoleData($data);
                    if ($validator->fails()) {
                        return json_encode(['status' => 422, 'message' => $validator->errors()->first()]);
                    }
                    
                    $role = $this->roleRepository->update($role->id, $data, $permission_ids);
                    $this->cacheManager->clearRoleCache();
                    return json_encode(['status' => 200, 'message' => 'Role updated successfully']);
                    
                default:
                    return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'message' => 'Failed to update ' . strtolower($model) . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Delete access (Permission or Role) - backward compatibility method
     * 
     * @param int $id
     * @param string $model 'Permission' or 'Role'
     * @return string JSON encoded response
     */
    public function deleteAccess(int $id, string $model): string
    {
        $model = ucfirst($model);
        
        try {
            switch ($model) {
                case 'Permission':
                    $permission = \Obrainwave\AccessTree\Models\Permission::find($id);
                    if (!$permission) {
                        return json_encode(['status' => 404, 'message' => 'Permission not found.']);
                    }
                    
                    // Check if permission is being used by any roles
                    $roleCount = \Obrainwave\AccessTree\Models\RoleHasPermission::where('permission_id', $id)->count();
                    if ($roleCount > 0) {
                        return json_encode(['status' => 422, 'message' => 'Cannot delete permission. It is assigned to ' . $roleCount . ' role(s).']);
                    }
                    
                    $deleted = $this->permissionRepository->delete($permission->id);
                    if ($deleted) {
                        $this->cacheManager->clearPermissionCache();
                        return json_encode(['status' => 200, 'message' => 'Permission deleted successfully']);
                    } else {
                        return json_encode(['status' => 500, 'message' => 'Failed to delete permission.']);
                    }
                    
                case 'Role':
                    $role = \Obrainwave\AccessTree\Models\Role::find($id);
                    if (!$role) {
                        return json_encode(['status' => 404, 'message' => 'Role not found.']);
                    }
                    
                    // Check if role is assigned to any users
                    $userCount = \Obrainwave\AccessTree\Models\UserRole::where('role_id', $id)->count();
                    if ($userCount > 0) {
                        return json_encode(['status' => 422, 'message' => 'Cannot delete role. It is assigned to ' . $userCount . ' user(s).']);
                    }
                    
                    $deleted = $this->roleRepository->delete($role->id);
                    if ($deleted) {
                        $this->cacheManager->clearRoleCache();
                        return json_encode(['status' => 200, 'message' => 'Role deleted successfully']);
                    } else {
                        return json_encode(['status' => 500, 'message' => 'Failed to delete role.']);
                    }
                    
                default:
                    return json_encode(['status' => 404, 'message' => 'Unknown `' . $model . '` model!']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => 500, 'message' => 'Failed to delete ' . strtolower($model) . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Get user model class dynamically
     * 
     * @return string
     */
    private function getUserModelClass(): string
    {
        return config('accesstree.user_model', 'App\\Models\\User');
    }
}
