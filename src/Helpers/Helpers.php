<?php

function createAccess(array $data, string $model, array $permission_ids = []) : String
{
    $model = ucfirst($model);
    switch($model)
    {
        case 'Permission':
            $permission = new \Obrainwave\AccessTree\Models\Permission();
            if($permission->where('name', $data['name'])->first())
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
            }
            $permission->name = $data['name'];
            $permission->slug = Illuminate\Support\Str::slug($data['name'], '_');
            $permission->status = $data['status'] ?? 0;
            $permission->save();
            return json_encode(['status' => 200, 'message' => 'Permission created successfully']);
            break;

        case 'Role':
            if(empty($permission_ids))
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! permission_ids cannot be empty.']);
            }
            $role = new \Obrainwave\AccessTree\Models\Role();
            if($role->where('name', $data['name'])->first())
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
            }
            $role->name = $data['name'];
            $role->slug = Illuminate\Support\Str::slug($data['name'], '_');
            $role->status = $data['status'] ?? 0;
            $role->save();

            foreach($permission_ids as $id)
            {
                if(!\Obrainwave\AccessTree\Models\Permission::find($id))
                {
                    continue;
                }
                \Obrainwave\AccessTree\Models\RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $id,
                ]);
            }
            return json_encode(['status' => 200, 'message' => 'Role created successfully']);
            break;

        default:
            return json_encode(['status' => 404, 'message' => 'Unknown `'.$model.'` model!']);
            break;
    }
}

function updateAccess(array $data, string $model, array $permission_ids = []) : String
{
    $model = ucfirst($model);
    switch($model)
    {
        case 'Permission':
            $permission = new \Obrainwave\AccessTree\Models\Permission();
            if($permission->where('name', $data['name'])->where('id', '!=', $data['data_id'])->first())
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! This permission has already been created.']);
            }
            $permission = $permission->find($data['data_id']);
            $permission->name = $data['name'];
            $permission->slug = Illuminate\Support\Str::slug($data['name'], '_');
            $permission->status = $data['status'] ?? 0;
            $permission->save();
            return json_encode(['status' => 200, 'message' => 'Permission updated successfully']);
            break;

        case 'Role':
            if(empty($permission_ids))
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! permission ids cannot be empty.']);
            }
            $check_role = new \Obrainwave\AccessTree\Models\Role();
            if($check_role->where('name', $data['name'])->where('id', '!=', $data['data_id'])->first())
            {
                return json_encode(['status' => 422, 'message' => 'Sorry! This role has already been created.']);
            }

            $role = $check_role->find($data['data_id']);
            $role->name = $data['name'];
            $role->slug = Illuminate\Support\Str::slug($data['name'], '_');
            $role->status = $data['status'] ?? 0;
            $role->save();

            $role_permission = new \Obrainwave\AccessTree\Models\RoleHasPermission();

            $role_permission->where('role_id', $role->id)->delete();

            foreach($permission_ids as $id)
            {
                $permission = new \Obrainwave\AccessTree\Models\Permission();
                if(!$permission::find($id))
                {
                    continue;
                }
                $role_permission::create([
                    'role_id' => $role->id,
                    'permission_id' => $id,
                ]);
            }
            return json_encode(['status' => 200, 'message' => 'Role updated successfully']);
            break;

        default:
            return json_encode(['status' => 404, 'message' => 'Unknown `'.$model.'` model!']);
            break;
    }
}

function createUserRole(array $roles, int $user_id) : String
{
    foreach($roles as $role)
    {
        $role = \Obrainwave\AccessTree\Models\Role::where('id', $role)->first();
        if($role)
        {
            $user_role = new \Obrainwave\AccessTree\Models\UserRole();
            $user_role->user_id = $user_id;
            $user_role->role_id = $role->id;
            $user_role->save();
        }else{
            continue;
        }

    }

    return json_encode(['status' => 200, 'message' => 'User Role(s) created successfully']);
}

function updateUserRole(array $roles, int $user_id) : String
{
    \Obrainwave\AccessTree\Models\UserRole::where('user_id', $user_id)->delete();
    foreach($roles as $role)
    {
        $role = \Obrainwave\AccessTree\Models\Role::where('id', $role)->first();
        if($role)
        {
            $user_role = new \Obrainwave\AccessTree\Models\UserRole();
            $user_role->user_id = $user_id;
            $user_role->role_id = $role->id;
            $user_role->save();
        }else{
            continue;
        }

    }

    return json_encode(['status' => 200, 'message' => 'User Role(s) updated successfully']);
}

function isRootUser(int $user_id) : bool
{
    if(\App\Models\User::where('id', $user_id)->where('is_root_user', true)->first())
    {
        return true;
    }

    return false;
}

function checkPermission(string $permission) : bool
{
    if(auth()->check())
    {
        $auth = auth()->user();
        $user_roles = \Obrainwave\AccessTree\Models\UserRole::where('user_id', $auth->id)->get();

        if(isRootUser($auth->id))
        {
            return true;
        }
        
    }else{
        return false;
    }
    
    $role_permissions = [];
    foreach($user_roles as $role)
    {
        $rolePermissions = \Obrainwave\AccessTree\Models\RoleHasPermission::where('role_id', $role->role_id)->get();
        
        foreach($rolePermissions as $rolePermission)
        {
            $role_permissions[] = $rolePermission->permission->slug;
        }
    }
    if(in_array($permission, $role_permissions))
    {
        return true;
    }
    return false;
}

function checkPermissions(array $permissions) : bool
{
    if(auth()->check())
    {
        $auth = auth()->user();
        $user_roles = \Obrainwave\AccessTree\Models\UserRole::where('user_id', $auth->id)->get();

        if(isRootUser($auth->id))
        {
            return true;
        }
        
    }else{
        return false;
    }
    
    $role_permissions = [];
    foreach($user_roles as $role)
    {
        $rolePermissions = \Obrainwave\AccessTree\Models\RoleHasPermission::where('role_id', $role->role_id)->get();
        
        foreach($rolePermissions as $rolePermission)
        {
            $role_permissions[] = $rolePermission->permission->slug;
        }
    }
    if(array_intersect($permissions, $role_permissions))
    {
        return true;
    }
    return false;
}