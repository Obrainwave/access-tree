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

function fetchRolePermissions(int $role_id) : Object
{
    $role_permissions = \Obrainwave\AccessTree\Models\RoleHasPermission::where('role_id', $role_id)->with(['permission', 'role'])->get();

    return $role_permissions;
}

function createUserRole(array $roles, int $user_id) : String
{
    foreach($roles as $role)
    {
        $role = \Obrainwave\AccessTree\Models\Role::where('id', $role)->first();
        if($role)
        { 
            $check_role = \Obrainwave\AccessTree\Models\UserRole::where('role_id', $role->id)->where('user_id', $user_id)->first();
            if($check_role)
            {
                continue;
            }
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

function fetchPermissions(int $status = null) : Object
{
    if($status == null)
    {
        $permissions = \Obrainwave\AccessTree\Models\Permission::get();
    }else{
        $permissions = \Obrainwave\AccessTree\Models\Permission::where('status', $status)->get();
    }

    return $permissions;
}

function fetchPermission(int $permission_id) : Object | null
{
    $permission = \Obrainwave\AccessTree\Models\Permission::find($permission_id);
    
    return $permission ?? null;
}

function fetchRoles(int $status = null) : Object
{
    if($status == null)
    {
        $roles = \Obrainwave\AccessTree\Models\Role::with('rolePermissions.permission')->get();
    }else{
        $roles = \Obrainwave\AccessTree\Models\Role::with('rolePermissions.permission')->where('status', $status)->get();
    }

    return $roles;
}

function fetchRole(int $role_id) : Object | null
{
    $role = \Obrainwave\AccessTree\Models\Role::where('id', $role_id)->with('rolePermissions.permission')->first();

    return $role ?? null;
}

function fetchUserRoles(int $user_id) : Object
{
    $user_roles = $roles = \Obrainwave\AccessTree\Models\UserRole::where('user_id', $user_id)->get();
    foreach($user_roles as $role)
    {
        $role['role'] = $role->role;
    }

    return $user_roles;
}

function importFile($request)
{
    $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );

    // if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $fileMimes))
    // {
 
    //     // Open uploaded CSV file with read-only mode
    //     $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

    //     // Skip the first line
    //     fgetcsv($csvFile);

    //     // Parse data from CSV file line by line        
    //     while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE)
    //     {
    //         // Get row data
    //         $name = $getData[0];
    //         $email = $getData[1];            

    //         // If user already exists in the database with the same email
    //         $query = "SELECT id FROM users WHERE email = '" . $getData[1] . "'";

    //         $check = mysqli_query($con, $query);

    //         if ($check->num_rows > 0)
    //         {
    //             mysqli_query($conn, "UPDATE users SET name = '" . $name . "', created_at = NOW() WHERE email = '" . $email . "'");
    //         }
    //         else
    //         {
    //             mysqli_query($con, "INSERT INTO users (name, email, created_at, updated_at) VALUES ('" . $name . "', '" . $email . "', NOW(), NOW())");
    //         }
    //     }

    //     // Close opened CSV file
    //     fclose($csvFile);

    //     header("Location: index.php");         
    // }else{
    //     echo "Please select valid file";
    // }
    $file = $request->file('file');
    $fileContents = file($file->getPathname());

    foreach ($fileContents as $line) {
        $data = str_getcsv($line);

        // Product::create([
        //     'name' => $data[0],
        //     'price' => $data[1],
        //     // Add more fields as needed
        // ]);
    }
}